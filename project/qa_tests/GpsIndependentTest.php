<?php

namespace Tests\Independent;

use App\Models\Agency;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GpsIndependentTest extends TestCase
{
    use RefreshDatabase;

    private function fleet(): array
    {
        $owner = User::factory()->agencyOwner()->create();
        $agency = Agency::factory()->create(['founder_id' => $owner->id]);
        $v = Vehicle::factory()->create(['agency_id' => $agency->id, 'has_gps' => true]);
        $this->actingAs($owner);
        return [$owner, $agency, $v, '/agency/vehicles/'.$v->uuid.'/gps'];
    }

    private function key(string $url): string
    {
        return $this->postJson($url.'/token')->assertOk()->json('token');
    }

    private function ingest(Vehicle $v, string $key, array $overrides = [])
    {
        return $this->postJson('/api/gps/vehicles/'.$v->uuid.'/positions', array_merge([
            'latitude' => 0, 'longitude' => 0, 'recorded_at' => now()->toIso8601String(),
        ], $overrides), ['Authorization' => 'Bearer '.$key]);
    }

    public function test_public_pages_and_serialization_do_not_disclose_private_coordinates_or_key(): void
    {
        [$owner, $agency, $v, $url] = $this->fleet();
        $key = $this->key($url);
        $this->ingest($v, $key, ['latitude' => 12.3456789, 'longitude' => 98.7654321])->assertOk();
        $encoded = $v->fresh()->toJson();
        foreach ([$key, hash('sha256', $key), '12.3456789', '98.7654321'] as $sensitive) {
            $this->assertStringNotContainsString($sensitive, $encoded);
            foreach (['/vehicles', '/vehicles/'.$v->uuid, '/agencies/'.$agency->slug] as $public) {
                $this->get($public)->assertOk()->assertDontSee($sensitive, false);
            }
        }
    }

    public function test_private_page_is_uncacheable_and_nonowner_mutations_are_forbidden(): void
    {
        [$owner, $agency, $v, $url] = $this->fleet();
        $page = $this->get($url)->assertOk();
        $this->assertStringContainsString('no-store', $page->headers->get('Cache-Control'));
        foreach ([User::factory()->client()->create(), User::factory()->agencyOwner()->create()] as $other) {
            $this->actingAs($other)->get($url)->assertForbidden();
            $this->getJson($url.'/position')->assertForbidden();
            $this->postJson($url.'/token')->assertForbidden();
            $this->deleteJson($url.'/token')->assertForbidden();
            $this->postJson($url.'/simulate', ['latitude' => 0, 'longitude' => 0, 'recorded_at' => now()->toIso8601String()])->assertForbidden();
        }
        $this->assertNull($v->fresh()->gps_token_hash);
        $this->assertNull($v->fresh()->gps_recorded_at);
    }

    public function test_staleness_uses_measurement_time_even_when_just_received_and_zero_is_valid(): void
    {
        $this->travelTo(now()->startOfSecond());
        [$owner, $agency, $v, $url] = $this->fleet();
        $key = $this->key($url);
        $this->ingest($v, $key, ['recorded_at' => now()->subSeconds(120)->toIso8601String()])->assertOk();
        $this->getJson($url.'/position')->assertOk()->assertJsonPath('status', 'stale')
            ->assertJsonPath('position.latitude', 0)->assertJsonPath('position.longitude', 0);
        $this->assertTrue($v->fresh()->gps_received_at->equalTo(now()));
        $this->ingest($v, $key, ['recorded_at' => now()->subSeconds(60)->toIso8601String()])->assertOk();
        $this->getJson($url.'/position')->assertJsonPath('status', 'recent');
        $this->travel(1)->seconds();
        $this->getJson($url.'/position')->assertJsonPath('status', 'stale');
    }

    public function test_same_timestamp_with_different_source_conflicts_without_replacing_position(): void
    {
        $this->travelTo(now()->startOfSecond());
        [$owner, $agency, $v, $url] = $this->fleet();
        $key = $this->key($url);
        $this->ingest($v, $key, ['source' => 'device'])->assertOk();
        $before = $this->getJson($url.'/position')->json('position');
        $this->ingest($v, $key, ['source' => 'simulation'])->assertStatus(409);
        $this->assertSame($before, $this->getJson($url.'/position')->json('position'));
    }

    public function test_invalid_payload_is_rejected_without_mutating_last_valid_measurement(): void
    {
        [$owner, $agency, $v, $url] = $this->fleet();
        $key = $this->key($url);
        $this->ingest($v, $key)->assertOk();
        $before = $this->getJson($url.'/position')->json('position');
        foreach ([['latitude' => 91], ['longitude' => -181], ['recorded_at' => 'not-iso'], ['source' => 'unknown']] as $bad) {
            $this->ingest($v, $key, $bad)->assertUnprocessable();
            $this->assertSame($before, $this->getJson($url.'/position')->json('position'));
        }
    }

    public function test_off_revokes_key_and_clear_position_then_on_does_not_restore_old_access(): void
    {
        [$owner, $agency, $v, $url] = $this->fleet();
        $key = $this->key($url);
        $this->ingest($v, $key)->assertOk();
        $v->refresh()->update(['has_gps' => false]);
        $this->getJson($url.'/position')->assertJsonPath('status', 'disabled')->assertJsonPath('position', null)->assertJsonPath('token_configured', false);
        $v->refresh()->update(['has_gps' => true]);
        $this->ingest($v, $key)->assertUnauthorized();
        $this->getJson($url.'/position')->assertJsonPath('status', 'no_signal')->assertJsonPath('position', null);
    }
}
