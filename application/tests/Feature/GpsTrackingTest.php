<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class GpsTrackingTest extends TestCase
{
    use RefreshDatabase;

    private function fleet(bool $gps = true): array
    {
        $owner = User::factory()->agencyOwner()->create();
        $agency = Agency::factory()->create(['founder_id' => $owner->id]);
        $vehicle = Vehicle::factory()->create(['agency_id' => $agency->id, 'has_gps' => $gps]);

        return [$owner, $agency, $vehicle];
    }

    private function url(Vehicle $v, string $suffix = 'position'): string
    {
        return '/agency/vehicles/'.$v->uuid.'/gps'.($suffix ? '/'.$suffix : '');
    }

    private function position(array $extra = []): array
    {
        return array_merge(['latitude' => 0, 'longitude' => 0, 'recorded_at' => now()->toIso8601String()], $extra);
    }

    private function key(User $owner, Vehicle $v): string
    {
        return $this->actingAs($owner)->postJson($this->url($v, 'token'))->assertOk()->assertHeader('Cache-Control', 'no-store, private')->json('token');
    }

    private function ingest(Vehicle $v, string $key, array $payload): TestResponse
    {
        return $this->postJson('/api/gps/vehicles/'.$v->uuid.'/positions', $payload, ['Authorization' => 'Bearer '.$key]);
    }

    public function test_owner_reads_empty_disabled_recent_and_stale_without_legacy_signal(): void
    {
        [$owner,$agency,$v] = $this->fleet();
        $v->forceFill(['gps_latitude' => 12, 'gps_longitude' => 34])->save();
        $this->actingAs($owner)->getJson($this->url($v))->assertOk()->assertJsonPath('status', 'no_signal')->assertJsonPath('position', null);
        $key = $this->key($owner, $v);
        $this->ingest($v, $key, $this->position())->assertOk();
        $this->getJson($this->url($v))->assertJsonPath('position.latitude', 0)->assertJsonPath('position.longitude', 0)->assertJsonPath('status', 'recent')->assertJsonPath('position.source', 'device');
        $this->travel(61)->seconds();
        $this->getJson($this->url($v))->assertJsonPath('status', 'stale');
        $v->refresh()->update(['has_gps' => false]);
        $this->getJson($this->url($v))->assertJsonPath('status', 'disabled')->assertJsonPath('position', null)->assertJsonPath('token_configured', false);
    }

    public function test_access_is_scoped_to_active_verified_founder_of_active_agency(): void
    {
        [$owner,$agency,$v] = $this->fleet();
        $this->getJson($this->url($v))->assertUnauthorized();
        $this->actingAs(User::factory()->superAdmin()->create())->getJson($this->url($v))->assertForbidden();
        $this->actingAs(User::factory()->agencyOwner()->create())->postJson($this->url($v, 'token'))->assertForbidden();
        $owner->update(['is_active' => false]);
        $this->actingAs($owner)->getJson($this->url($v))->assertForbidden();
        $owner->update(['is_active' => true]);
        $owner->forceFill(['email_verified_at' => null])->save();
        $this->actingAs($owner)->getJson($this->url($v))->assertForbidden();
        $owner->forceFill(['email_verified_at' => now()])->save();
        $agency->update(['status' => 'suspended']);
        $this->actingAs($owner)->getJson($this->url($v))->assertForbidden();
    }

    public function test_token_rotation_revocation_scope_and_secret_hiding(): void
    {
        [$owner,$agency,$v] = $this->fleet();
        $key = $this->key($owner, $v);
        $this->assertSame(hash('sha256', $key), $v->fresh()->gps_token_hash);
        $this->assertArrayNotHasKey('gps_token_hash', $v->fresh()->toArray());
        $this->assertArrayNotHasKey('gps_latitude', $v->fresh()->toArray());
        $other = Vehicle::factory()->create(['agency_id' => $agency->id, 'has_gps' => true]);
        $this->ingest($other, $key, $this->position())->assertUnauthorized();
        $new = $this->key($owner, $v);
        $this->assertNotSame($key, $new);
        $this->ingest($v, $key, $this->position())->assertUnauthorized();
        $this->deleteJson($this->url($v, 'token'))->assertOk();
        $this->ingest($v, $new, $this->position())->assertUnauthorized();
    }

    public function test_api_requires_bearer_and_rechecks_owner_and_agency(): void
    {
        [$owner,$agency,$v] = $this->fleet();
        $key = $this->key($owner, $v);
        $this->postJson('/api/gps/vehicles/'.$v->uuid.'/positions', $this->position())->assertUnauthorized();
        $this->ingest($v, 'wrong', $this->position())->assertUnauthorized();
        $owner->update(['is_active' => false]);
        $this->ingest($v, $key, $this->position())->assertForbidden();
        $owner->update(['is_active' => true]);
        $agency->update(['status' => 'suspended']);
        $this->ingest($v, $key, $this->position())->assertForbidden();
    }

    public function test_payload_boundaries_timestamp_window_and_ordering(): void
    {
        $this->travelTo(now()->startOfSecond());
        [$owner,$agency,$v] = $this->fleet();
        $key = $this->key($owner, $v);
        foreach ([['latitude' => 91], ['longitude' => -181], ['latitude' => 'bad'], ['recorded_at' => '2026-10-25 12:00:00'], ['recorded_at' => now()->subHours(25)->toIso8601String()], ['recorded_at' => now()->addSeconds(31)->toIso8601String()], ['source' => 'fabricated']] as $bad) {
            $this->ingest($v, $key, $this->position($bad))->assertUnprocessable();
        }
        $payload = $this->position(['latitude' => 90, 'longitude' => -180]);
        $this->ingest($v, $key, $payload)->assertOk();
        $received = $v->fresh()->gps_received_at;
        $this->travel(2)->seconds();
        $this->ingest($v, $key, $payload)->assertOk();
        $this->assertTrue($received->equalTo($v->fresh()->gps_received_at));
        $this->ingest($v, $key, array_merge($payload, ['latitude' => 0]))->assertStatus(409);
        $this->ingest($v, $key, $this->position(['recorded_at' => now()->subSeconds(3)->toIso8601String()]))->assertStatus(409);
    }

    public function test_simulation_gate_device_conflict_and_forced_label(): void
    {
        [$owner,$agency,$v] = $this->fleet();
        $this->actingAs($owner);
        config(['gps.allow_simulation' => false]);
        $this->postJson($this->url($v, 'simulate'), $this->position())->assertForbidden();
        config(['gps.allow_simulation' => true]);
        $this->postJson($this->url($v, 'simulate'), $this->position(['source' => 'device']))->assertOk()->assertJsonPath('position.source', 'simulation');
        $key = $this->key($owner, $v);
        $this->getJson($this->url($v))->assertJsonPath('simulation_allowed', false);
        $this->postJson($this->url($v, 'simulate'), $this->position())->assertStatus(409);
        $this->deleteJson($this->url($v, 'token'))->assertOk();
        $this->getJson($this->url($v))->assertJsonPath('simulation_allowed', true);
        $v->refresh()->update(['has_gps' => false]);
        $this->postJson($this->url($v, 'simulate'), $this->position())->assertStatus(409);
        $this->postJson($this->url($v, 'token'))->assertStatus(409);
    }

    private function form(array $extra = []): array
    {
        return array_merge(['brand' => 'Dacia', 'model' => 'Sandero', 'year' => 2026, 'color' => 'Blanc', 'condition' => 'good', 'seats' => 5, 'fuel_type' => 'gasoline', 'transmission' => 'manual', 'price_per_day' => 300, 'has_gps' => '0', 'is_featured' => '0', 'status' => 'available'], $extra);
    }

    public function test_exact_timestamp_window_boundaries_are_accepted_and_missing_fields_are_rejected(): void
    {
        $this->travelTo(now()->startOfSecond());
        [$owner,$agency,$v] = $this->fleet();
        $key = $this->key($owner, $v);
        $this->ingest($v, $key, [])->assertUnprocessable()->assertJsonValidationErrors(['latitude', 'longitude', 'recorded_at']);
        $this->ingest($v, $key, $this->position(['latitude' => -90, 'longitude' => 180, 'recorded_at' => now()->subHours(24)->toIso8601String()]))->assertOk();
        $this->ingest($v, $key, $this->position(['recorded_at' => now()->addSeconds(30)->toIso8601String()]))->assertOk();
    }

    public function test_disabled_vehicle_mutations_and_simulation_outside_local_environment_are_refused(): void
    {
        [$owner,$agency,$v] = $this->fleet(false);
        $this->actingAs($owner);
        $this->getJson($this->url($v))->assertOk()->assertJsonPath('status', 'disabled')->assertJsonPath('simulation_allowed', false);
        $this->postJson($this->url($v, 'token'))->assertStatus(409);
        $this->deleteJson($this->url($v, 'token'))->assertStatus(409);
        config(['gps.allow_simulation' => false]);
        $this->postJson($this->url($v, 'simulate'), $this->position())->assertStatus(409);
        config(['gps.allow_simulation' => true]);
        $this->postJson($this->url($v, 'simulate'), $this->position())->assertStatus(409);
        $v->update(['has_gps' => true]);
        $this->withSession(['_token' => 'synthetic-gps-csrf-token']);
        $this->app->instance('env', 'production');
        $this->postJson($this->url($v, 'simulate'), $this->position(['_token' => 'synthetic-gps-csrf-token']))->assertForbidden();
    }

    public function test_ingestion_rate_is_limited_without_mutating_position(): void
    {
        [$owner,$agency,$v] = $this->fleet();
        $key = $this->key($owner, $v);
        $this->ingest($v, $key, $this->position())->assertOk();
        for ($i = 0; $i < 59; $i++) {
            $this->ingest($v, 'invalid', $this->position())->assertUnauthorized();
        }
        $this->ingest($v, $key, $this->position())->assertStatus(429);
        $this->assertSame('device', $v->fresh()->gps_source);
    }

    public function test_form_boolean_creation_and_malicious_gps_fields_ignored(): void
    {
        Storage::fake('public');
        [$owner,$agency,$v] = $this->fleet();
        $this->actingAs($owner);
        $this->post('/vehicles', $this->form(['registration_number' => 'GPS-CREATE', 'photos' => [UploadedFile::fake()->image('car.jpg')], 'gps_latitude' => 44, 'gps_token_hash' => 'forged']))->assertSessionHasNoErrors()->assertRedirect();
        $created = Vehicle::where('registration_number', 'GPS-CREATE')->firstOrFail();
        $this->assertFalse($created->has_gps);
        $this->assertNull($created->gps_latitude);
        $this->assertNull($created->gps_token_hash);
        $this->post('/vehicles', $this->form(['has_gps' => '1', 'registration_number' => 'GPS-CREATE-YES', 'photos' => [UploadedFile::fake()->image('car.jpg')]]))->assertSessionHasNoErrors()->assertRedirect();
        $this->assertTrue(Vehicle::where('registration_number', 'GPS-CREATE-YES')->firstOrFail()->has_gps);
    }

    public function test_form_off_clears_position_revokes_key_and_on_does_not_restore_it(): void
    {
        [$owner,$agency,$v] = $this->fleet();
        $key = $this->key($owner, $v);
        $this->ingest($v, $key, $this->position())->assertOk();
        $this->patch('/vehicles/'.$v->uuid, $this->form())->assertSessionHasNoErrors()->assertRedirect();
        $v->refresh();
        $this->assertFalse($v->has_gps);
        $this->assertNull($v->gps_token_hash);
        $this->assertNull($v->gps_latitude);
        $this->assertNull($v->gps_recorded_at);
        $this->patch('/vehicles/'.$v->uuid,$this->form(['has_gps' => '1', 'gps_latitude' => 22]))->assertSessionHasNoErrors()->assertRedirect();
        $this->ingest($v,$key,$this->position())->assertUnauthorized();
        $this->assertNull($v->fresh()->gps_latitude);
    }
}
