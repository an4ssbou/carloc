<?php
namespace Database\Seeders;
use App\Models\{User,Agency,Vehicle,VehiclePhoto};
use App\Support\PublicDemoPolicy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{DB,Hash,Storage};
use Illuminate\Support\Str;
final class PublicDemoSeeder extends Seeder {
 public function run():void {
  PublicDemoPolicy::validate(['enabled'=>(bool)config('demo.enabled'),'environment'=>config('app.env'),'debug'=>config('app.debug'),'connection'=>config('database.default'),'database'=>config('database.connections.sqlite.database'),'url'=>config('app.url'),'mailer'=>config('mail.default'),'allow_local'=>config('demo.allow_local')]);
  if(DB::table('public_demo_provenance')->where('id',1)->value('value')!=='carloc-public-demo-v1')throw new \RuntimeException('Provenance démo refusée.');
  $assets=array_slice(json_decode(file_get_contents(resource_path('demo/vehicles.json')),true,512,JSON_THROW_ON_ERROR),0,3);
  foreach($assets as$a)if(($a['license']??'')!=='CC0'||!is_file(public_path($a['asset']))||hash_file('sha256',public_path($a['asset']))!==$a['sha256'])throw new \RuntimeException('Illustration indisponible.');
  DB::transaction(function()use($assets){
   $this->call(RoleSeeder::class);
   foreach(['cin','permis']as$f)Storage::disk('local')->put('public-demo/'.$f.'.txt','DOCUMENT FICTIF DE DEMONSTRATION — aucune identité réelle.');
   $users=[];
   foreach(['client'=>'client','agence'=>'agency_owner','admin'=>'super_admin']as$key=>$role){
    $u=User::firstOrNew(['email'=>$key.'@demo.carloc.test']);
    if(!$u->exists){$u->forceFill(['first_name'=>ucfirst($key),'last_name'=>'Fictif','role'=>$role,'cin_number'=>'DEMO-'.strtoupper($key),'cin_photo'=>'public-demo/cin.txt','license_photo'=>'public-demo/permis.txt','email_verified_at'=>now(),'is_active'=>true,'has_store'=>$role==='agency_owner','password'=>Hash::make('Demo-Portfolio-2026!')])->saveQuietly();$u->syncRoles([$role]);}
    $users[$key]=$u;
   }
   $agency=Agency::where('slug','agence-demo-publique')->first();
   if(!$agency)$agency=Agency::createQuietly(['name'=>'Agence fictive CarLoc','slug'=>'agence-demo-publique','city'=>'Casablanca','address'=>'Adresse fictive de démonstration','email'=>'agence@demo.carloc.test','founder_id'=>$users['agence']->id,'status'=>'active','description'=>'Démonstration partagée — aucune offre réelle.']);
   foreach($assets as$i=>$a){
    if(Vehicle::where('registration_number','PUBLIC-DEMO-'.($i+1))->exists())continue;
    $car=Vehicle::createQuietly(['uuid'=>(string)Str::uuid(),'agency_id'=>$agency->id,'brand'=>$a['brand'],'model'=>$a['model'],'year'=>$a['year'],'registration_number'=>'PUBLIC-DEMO-'.($i+1),'mileage'=>10000,'color'=>$a['color'],'condition'=>'good','seats'=>5,'fuel_type'=>$a['fuel_type'],'transmission'=>$a['transmission'],'price_per_day'=>$a['price_per_day'],'price_week'=>$a['price_per_day']*6,'price_two_weeks'=>$a['price_per_day']*11,'price_month'=>$a['price_per_day']*20,'has_gps'=>false,'is_featured'=>$i===0,'description'=>'Véhicule fictif; photographie illustrative.','status'=>'available','is_manually_unavailable'=>false]);
    $path='public-demo/vehicles/'.$a['key'].'.jpg';Storage::disk('public')->put($path,file_get_contents(public_path($a['asset'])));VehiclePhoto::createQuietly(['vehicle_id'=>$car->id,'path'=>$path,'is_primary'=>true,'order'=>0]);
   }
   $agency->updateQuietly(['total_vehicles'=>$agency->vehicles()->count(),'total_reservations'=>$agency->reservations()->count()]);
  });
 }
}
