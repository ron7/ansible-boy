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

if(strlen($_SESSION['username'])>2){

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

  Flight::render('template', compact('u', 'webroot', 'baseUrl', 'sitename', 'my', 'defaulthashes'));
  Flight::start();
}else{
  include_once('views/login.php');
}
