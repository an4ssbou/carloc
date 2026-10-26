<?php
require '/var/www/html/app/Support/PublicDemoPolicy.php';
use App\Support\PublicDemoPolicy;
try {
 $bool=fn($name)=>filter_var(getenv($name)?:false,FILTER_VALIDATE_BOOLEAN);
 PublicDemoPolicy::validate(['enabled'=>$bool('DEMO_MODE'),'environment'=>getenv('APP_ENV'),'debug'=>$bool('APP_DEBUG'),'connection'=>getenv('DB_CONNECTION'),'database'=>getenv('DB_DATABASE'),'url'=>getenv('APP_URL'),'mailer'=>getenv('MAIL_MAILER'),'allow_local'=>$bool('DEMO_ALLOW_LOCAL_HTTP')]);
 if(getenv('DB_URL') || is_link(PublicDemoPolicy::DATABASE))throw new RuntimeException('Refus configuration DB.');
 $file=PublicDemoPolicy::DATABASE;
 if(file_exists($file)) {
  $pdo=new PDO('sqlite:'.$file,null,null,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
  if($pdo->query('SELECT value FROM public_demo_provenance WHERE id=1')->fetchColumn()!=='carloc-public-demo-v1')throw new RuntimeException('Base étrangère.');
  $pdo=null;
  foreach([$file.'-wal',$file.'-shm',$file]as$p){if(is_link($p))throw new RuntimeException('Lien DB refusé.');if(file_exists($p)&&!unlink($p))throw new RuntimeException('Reset refusé.');}
 }
 $pdo=new PDO('sqlite:'.$file,null,null,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
 $pdo->exec('CREATE TABLE public_demo_provenance (id INTEGER PRIMARY KEY, value TEXT NOT NULL)');
 $pdo->exec("INSERT INTO public_demo_provenance VALUES (1,'carloc-public-demo-v1')");
}catch(Throwable $e){fwrite(STDERR,"Démarrage démo refusé: configuration ou provenance non autorisée.\n");exit(1);}
