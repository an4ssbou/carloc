<?php
namespace App\Support;
final class PublicDemoPolicy {
 public const DATABASE='/tmp/carloc-demo.sqlite';
 public const ACCOUNTS=['client@demo.carloc.test','agence@demo.carloc.test','admin@demo.carloc.test'];
 public static function validate(array $c):void {
  $url=parse_url((string)($c['url']??''));
  $local=($c['allow_local']??false)===true && in_array($url['host']??'', ['127.0.0.1','localhost'],true) && ($url['scheme']??'')==='http';
  if (($c['allow_local']??false) && !$local) throw new \RuntimeException('HTTP local réservé à la boucle locale.');
  if(($c['enabled']??false)!==true || ($c['environment']??'')!=='production' || ($c['debug']??true)!==false || ($c['connection']??'')!=='sqlite' || ($c['database']??'')!==self::DATABASE || ($c['mailer']??'')!=='array' || (!$local && (($url['scheme']??'')!=='https' || empty($url['host'])))) throw new \RuntimeException('Configuration de démonstration refusée.');
 }
 public static function allowsMutation(string $method,?string $name):bool {
  return $method==='POST' && in_array($name,['login.submit','logout','reservation.store','reservation.cancel','agency.reservations.confirm','agency.reservations.cancel','admin.reservations.confirm','admin.reservations.cancel'],true);
 }
 public static function allowsAccount(string $email):bool {return in_array(strtolower($email),self::ACCOUNTS,true);}
}
