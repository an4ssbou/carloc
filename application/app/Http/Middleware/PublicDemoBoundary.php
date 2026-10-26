<?php
namespace App\Http\Middleware;
use App\Support\PublicDemoPolicy;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;
final class PublicDemoBoundary {
 public function handle(Request $request,Closure $next):Response {
  if(!config('demo.enabled')) return $next($request);
  abort_if((int)$request->server('CONTENT_LENGTH',0)>16384,413);
  $name=$request->route()?->getName();
  $blocked=['register','password.request','password.reset','verification.notice','password.confirm','profile.edit','profile.license_photo','profile.cin_photo','vehicles.create','vehicles.edit','agency.create','agency.edit','admin.agencies.edit','contact'];
  abort_if(in_array($name,$blocked,true)||str_starts_with((string)$name,'agency.vehicles.gps'),403,'Fonction désactivée dans la démonstration publique.');
  $user=$request->user();
  abort_if($user&&!PublicDemoPolicy::allowsAccount($user->email),403);
  if(!in_array($request->method(),['GET','HEAD','OPTIONS'],true)) {
   abort_unless(PublicDemoPolicy::allowsMutation($request->method(),$name),403,'Cette action est désactivée dans la démonstration.');
   abort_if(count($request->allFiles())>0,403);
   if($name==='login.submit') abort_unless(PublicDemoPolicy::allowsAccount((string)$request->input('email')),403,'Utilisez un compte fictif indiqué.');
   if($name!=='login.submit') abort_unless($user,401);
   $keys=['demo-ip:'.hash('sha256',(string)$request->ip())];if($user)$keys[]='demo-user:'.$user->id;
   foreach($keys as$key) abort_if(RateLimiter::tooManyAttempts($key,30),429,'Limite de démonstration atteinte.');
   foreach($keys as$key) RateLimiter::hit($key,60);
   if($name==='reservation.store') {
    abort_unless($user->role==='client',403);
    abort_if(count(array_diff(array_keys($request->all()),['_token','vehicle_id','start_date','end_date']))>0,422,'Seuls le véhicule et les dates sont acceptés.');
   }
  }
  $response=$next($request);
  $response->headers->set('X-Robots-Tag','noindex, nofollow');
  if(!config('demo.allow_local'))$response->headers->set('Strict-Transport-Security','max-age=31536000');
  return $response;
 }
}
