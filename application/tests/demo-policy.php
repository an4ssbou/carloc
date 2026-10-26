<?php
$source=__DIR__.'/../app/Support/PublicDemoPolicy.php';
if (!is_file($source)) { fwrite(STDERR,"FAIL: public demo configuration and mutation boundary missing\n"); exit(1); }
require $source;
use App\Support\PublicDemoPolicy;
$n=0;
function check(bool $ok,string $message):void {global $n;$n++;if(!$ok){fwrite(STDERR,"FAIL: $message\n");exit(1);}}
$valid=['enabled'=>true,'environment'=>'production','debug'=>false,'connection'=>'sqlite','database'=>'/tmp/carloc-demo.sqlite','url'=>'https://carloc-portfolio-demo.onrender.com','mailer'=>'array'];
PublicDemoPolicy::validate($valid);
foreach([['enabled',false],['environment','local'],['debug',true],['connection','mysql'],['database','/tmp/foreign.sqlite'],['database','/tmp/../tmp/carloc-demo.sqlite'],['url','http://public.example'],['mailer','smtp']]as[$k,$v]){
 $blocked=false;try{PublicDemoPolicy::validate(array_replace($valid,[$k=>$v]));}catch(RuntimeException $e){$blocked=true;}check($blocked,"unsafe $k");
}
foreach(['login.submit','logout','reservation.store','reservation.cancel','agency.reservations.confirm','agency.reservations.cancel','admin.reservations.confirm','admin.reservations.cancel'] as $r){check(PublicDemoPolicy::allowsMutation('POST',$r),$r);check(!PublicDemoPolicy::allowsMutation('DELETE',$r),'method boundary');}
foreach(['register','password.update','profile.update','contact.send','agency.store','vehicles.store','vehicles.photos.store','gps.positions.store','agency.vehicles.gps.token','payment.pay','reservation.simulate-payment',null]as$r){check(!PublicDemoPolicy::allowsMutation('POST',$r),'deny unlisted route');}
check(PublicDemoPolicy::allowsAccount('client@demo.carloc.test'),'synthetic account');
check(!PublicDemoPolicy::allowsAccount('someone@example.com'),'foreign account');
echo "PASS: $n public demo checks\n";
