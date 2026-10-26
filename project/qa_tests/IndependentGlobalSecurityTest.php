<?php
namespace Tests\Independent;
use App\Models\Agency;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehiclePhoto;
use App\Services\ReservationTransitionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
class IndependentGlobalSecurityTest extends TestCase {
 use RefreshDatabase;
 private function fleet(): array {
  $owner=User::factory()->agencyOwner()->create();
  $agency=Agency::factory()->create(['founder_id'=>$owner->id]);
  return [$owner,$agency,Vehicle::factory()->create(['agency_id'=>$agency->id])];
 }
 public function test_cross_agency_reservation_mutations_and_reads_are_forbidden(): void {
  [$owner,$agency,$v]=$this->fleet(); [$other,$otherAgency]=$this->fleet();
  $r=Reservation::factory()->create(['vehicle_id'=>$v->id,'agency_id'=>$agency->id,'status'=>'pending']);
  $this->actingAs($other)->get('/agency/'.$agency->slug.'/reservations')->assertForbidden();
  foreach(['confirm','cancel'] as $action) $this->post('/agency/'.$otherAgency->slug.'/reservations/'.$r->id.'/'.$action)->assertForbidden();
  $this->assertSame('pending',$r->fresh()->status); $this->assertNull($r->fresh()->confirmed_at); $this->assertNull($r->fresh()->cancelled_at);
 }
 public function test_cross_vehicle_photo_ids_cannot_mutate_or_delete(): void {
  Storage::fake('public'); [$owner,$agency,$v]=$this->fleet(); [$other,$otherAgency,$otherV]=$this->fleet();
  Storage::disk('public')->put('vehicles/qa.jpg','synthetic');
  $photo=VehiclePhoto::create(['vehicle_id'=>$v->id,'path'=>'vehicles/qa.jpg','is_primary'=>true,'order'=>0]);
  $this->actingAs($other)->delete('/vehicles/'.$v->uuid)->assertForbidden();
  $this->delete('/vehicles/'.$v->uuid.'/photos/'.$photo->id)->assertForbidden();
  $this->patch('/vehicles/'.$otherV->uuid.'/photos/'.$photo->id.'/primary')->assertNotFound();
  $this->assertNotNull($v->fresh()); $this->assertNotNull($photo->fresh()); Storage::disk('public')->assertExists('vehicles/qa.jpg');
 }
 public function test_profile_injected_privileges_cannot_access_admin(): void {
  $u=User::factory()->client()->create(); $this->actingAs($u);
  foreach(['dashboard','users','agencies','vehicles','reservations'] as $page) $this->get('/admin/'.$page)->assertForbidden();
  $this->patch('/profile',['first_name'=>'Client','last_name'=>'Synthetic','email'=>$u->email,'phone'=>$u->phone,'role'=>'super_admin','has_store'=>true,'is_active'=>false])->assertSessionHasNoErrors();
  $this->assertSame('client',$u->fresh()->role); $this->assertFalse($u->fresh()->has_store); $this->assertTrue($u->fresh()->is_active); $this->get('/admin/users')->assertForbidden();
 }
 public function test_svg_uploads_refused_without_mutation(): void {
  Storage::fake('public'); Storage::fake('local'); [$owner,$agency,$v]=$this->fleet();
  $svg='<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>';
  $this->actingAs($owner)->post('/vehicles/'.$v->uuid.'/photos',['photos'=>[UploadedFile::fake()->createWithContent('attack.svg',$svg)]])->assertSessionHasErrors('photos.0');
  $this->patch('/profile',['first_name'=>'Owner','last_name'=>'Synthetic','email'=>$owner->email,'phone'=>$owner->phone,'cin_photo'=>UploadedFile::fake()->createWithContent('attack.svg',$svg)])->assertSessionHasErrors('cin_photo');
  $this->assertSame([],Storage::disk('public')->allFiles()); $this->assertSame([],Storage::disk('local')->allFiles()); $this->assertNull($owner->fresh()->cin_photo); $this->assertSame(0,$v->photos()->count());
 }
 public function test_password_change_revokes_session_without_fresh_acting_as(): void {
  $u=User::factory()->create(); $this->actingAs($u)->get('/profile')->assertOk();
  $u->forceFill(['password'=>Hash::make('QA-Changed-2026!')])->save();
  $this->getJson('/profile')->assertUnauthorized(); $this->assertGuest();
 }
 public function test_stale_reservation_cannot_reactivate_cancelled_record(): void {
  [$owner,$agency,$v]=$this->fleet(); $r=Reservation::factory()->create(['vehicle_id'=>$v->id,'agency_id'=>$agency->id,'status'=>'pending']);
  $stale=Reservation::findOrFail($r->id); $svc=app(ReservationTransitionService::class);
  $this->assertNotNull($svc->cancel($r,'agency')); $this->assertNull($svc->confirm($stale)); $this->assertSame('cancelled',$r->fresh()->status); $this->assertNull($r->fresh()->confirmed_at);
 }
}
