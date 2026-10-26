<?php
namespace Tests\Independent;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Reservation;
use Database\Seeders\ShowcaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
class ShowcaseIndependentTest extends TestCase {
 use RefreshDatabase;
 private function init(): array {
  $this->travelTo(\Carbon\Carbon::parse('2026-10-25 12:00:00','UTC'));
  Mail::fake(); Notification::fake(); Storage::fake('local'); Storage::fake('public');
  $this->seed(ShowcaseSeeder::class);
  $ledger=DB::table('demo_datasets')->where('name','showcase-v1')->first();
  $this->assertNotNull($ledger);
  return [json_decode($ledger->records,true,512,JSON_THROW_ON_ERROR),$ledger];
 }
 private function counts(): array {
  $out=[];foreach(['users','agencies','vehicles','vehicle_photos','reservations','demo_datasets'] as $table)$out[$table]=DB::table($table)->count();return $out;
 }
 public function test_second_run_preserves_existing_custom_data_ledger_and_counts_without_messages(): void {
  $sentinel=User::factory()->create(['first_name'=>'Existing','last_name'=>'Untouched']);
  [$ids,$ledger]=$this->init();
  foreach(['clients'=>40,'agencies'=>4,'vehicles'=>24,'photos'=>25,'reservations'=>180] as $key=>$n){$this->assertArrayHasKey($key,$ids);$this->assertCount($n,$ids[$key]);}
  $v=Vehicle::findOrFail($ids['vehicles'][0]);$v->updateQuietly(['price_per_day'=>987.65]);
  $before=$this->counts();$sentinelBefore=$sentinel->fresh()->getRawOriginal();
  $this->seed(ShowcaseSeeder::class);
  $this->assertSame($before,$this->counts());
  $this->assertSame('987.65',$v->fresh()->price_per_day);
  $this->assertSame($sentinelBefore,$sentinel->fresh()->getRawOriginal());
  $this->assertSame($ledger->records,DB::table('demo_datasets')->where('name','showcase-v1')->value('records'));
  Mail::assertNothingSent();Mail::assertNothingQueued();Notification::assertNothingSent();
 }
 public function test_six_months_prices_statuses_and_active_intervals_are_coherent(): void {
  [$ids,$ledger]=$this->init();
  $reservations=Reservation::with('vehicle')->whereIn('id',$ids['reservations'])->get();
  $this->assertCount(180,$reservations);
  $months=DB::table('reservations')->whereIn('id',$ids['reservations'])->whereIn('status',['confirmed','completed'])->selectRaw("DATE_FORMAT(start_date,'%Y-%m') AS month, SUM(total_price) AS revenue")->groupBy('month')->orderBy('month')->get();
  $this->assertCount(6,$months);foreach($months as $month)$this->assertGreaterThan(0,(float)$month->revenue);
  foreach($reservations as $r){
   $days=(int)$r->start_date->diffInDays($r->end_date);
   $this->assertGreaterThanOrEqual(1,$days);$this->assertLessThanOrEqual(30,$days);
   $this->assertEqualsWithDelta($r->vehicle->calculateRentalTotal($days),(float)$r->total_price,0.01);
   $this->assertTrue($r->created_at->lt($r->start_date));
   if($r->status==='completed'){$this->assertTrue($r->end_date->lt(now()));$this->assertNotNull($r->completed_at);}
   if($r->status==='confirmed'){$this->assertNotNull($r->confirmed_at);$this->assertNull($r->cancelled_at);}
   if($r->status==='pending'){$this->assertNull($r->confirmed_at);$this->assertNull($r->cancelled_at);}
   if($r->status==='cancelled'){$this->assertNotNull($r->cancelled_at);$this->assertNotEmpty($r->cancelled_by);}
  }
  $active=$reservations->whereIn('status',['pending','confirmed'])->values();
  foreach($active as $i=>$a)foreach($active as $j=>$b)if($j>$i&&$a->vehicle_id===$b->vehicle_id)$this->assertFalse($a->start_date->lte($b->end_date)&&$b->start_date->lte($a->end_date),'Inclusive active rental intervals overlap');
  Mail::assertNothingSent();Notification::assertNothingSent();
 }
 public function test_missing_ledger_with_existing_showcase_records_refuses_collision_without_mutation(): void {
  [$ids,$ledger]=$this->init();
  DB::table('demo_datasets')->where('name','showcase-v1')->delete();
  $before=$this->counts();$price=Vehicle::findOrFail($ids['vehicles'][0])->price_per_day;
  $caught=false;try{$this->seed(ShowcaseSeeder::class);}catch(\RuntimeException $e){$caught=true;}
  $this->assertTrue($caught,'A missing ledger must not claim existing fixture records');
  $this->assertSame($before,$this->counts());$this->assertSame($price,Vehicle::findOrFail($ids['vehicles'][0])->price_per_day);
  Mail::assertNothingSent();Notification::assertNothingSent();
 }
}
