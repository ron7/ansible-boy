<?php
require_once("global_functions.php");

$webroot= str_replace($_SERVER['DOCUMENT_ROOT'], "", dirname(__FILE__));
$u = url($webroot);
if($u[0]=='logout'){
  session_destroy();
  header('Location: //'.$_SERVER["SERVER_NAME"]);
  die();
}

$h = getallheaders();
if(in_array($h['Token'],$tokens)){
$_SESSION['username'] = 'api';
}

/* if($u[0]==''){ */
/*   include_once(".{$baseUrl}ind.php"); */
/*   die(); */
/* } */
if(strlen($_SESSION['username'])>2){

  /* if($u[0]==='clients' && $u[1] === 'get' && $u[2]<>''){ */
  /*   header('Content-Type: application/json'); */
  /*   $cid = intval($u[2]); */
  /*   /1* $clientsList = mql("select * from clients where id=$cid limit 1"); *1/ */
  /*   $clientsList = mql("select c.*,i.currency from clients c left join invoices i on c.id=i.clientid where c.id=$cid limit 1"); */
  /*   echo json_encode($clientsList,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); */
  /*   die(); */
  /* } */

  require 'flight/Flight.php';
  Flight::set('flight.log_errors', true);
  Flight::map('error', function(Throwable $ex){ echo $ex->getTraceAsString(); });

  Flight::route('/', function(){
  });
  Flight::route('*', function(){
  });
  if (empty($_SESSION['username'])) {
    require ('views/login.php');
    exit;
  }
  foreach($_REQUEST as $k=>$v) $$k=$v; //to use $_REQUEST["example"] as $example


  /* Flight::render('/template', array('u' => $u, 'webroot' => $webroot, 'baseUrl'=> $baseUrl, 'sitename' => $sitename, 'apiPullKey' => $apiPullKey, 'archiveBase' => $archiveBase, 'my' => $my, )); */
  Flight::render('template', compact('u', 'webroot', 'baseUrl', 'sitename', 'my'));
  Flight::start();
}else{
  include_once('views/login.php');
}
