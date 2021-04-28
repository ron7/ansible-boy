<?php
/* use PHPMailer\PHPMailer\PHPMailer; */
/* use PHPMailer\PHPMailer\Exception; */

date_default_timezone_set('Europe/Sofia');
setlocale(LC_ALL, 'bg_BG');

$minify_html=0;
$debug=1;
$debugIps= array('127.0.0.1');
$sitename = 'X'; //default, overwritten by .env

$tokens = array(
'tokenLKDJHFYUUHIEUIEJOIJOIJDDOJSHDK'
);

//can I utilize these in a good way?

$app['site_name'] = $sitename; // but overwritable via env ?
  $app['site_title'] = $app['site_name'];
  $app['site_description'] = 'Next great thing';
  $app['site_keywords'] = '';

//echo genPass('admin')."\n\n";

function authenticate($user, $pass) {
  global $allowed_dept,$admins_dept,$admins,$ldapServer;
  // check whether user is allowed and if credentials are OK
  $user = clean($user,'@\.\-\_');
  $pass = clean($pass,'\_\-\@\.\,\ \"\#\!\$\%\&\*');
  $q = "select * from users where (username='$user' or email='$user') limit 1";
  $qr = mql($q);
  if(
    (hashPass($qr['password'],$pass)) // check if the DB pass matches what we provided
  ){
    return true;
  }else{
    return false;
  }
  return false;
}

function getAllUsers($userid=''){
  if((isset($userid)) and (intval($userid>0))){
    $user=" where u.id='".intval($userid)."' limit 1";
  }
  $q = "select * from users u $user";
  $r = mql($q);

  if((isset($userid)) and (intval($userid)>0)){   // single rec/user

    switch ($r['status']){
    case 0: $r['state']='<span class="label label-danger">No</span>'; $r['state_class']='danger'; break;
    case 1: $r['state']='<span class="label label-success">Yes</span>'; $r['state_class']=''; break;
    }

    switch ($r['admin']){
    case 0: $r['is-admin']='<span class="label label-danger">No</span>'; break;
    case 1: $r['is-admin']='<span class="label label-success">Yes</span>'; break;
    }

    return $r;
  }else{
    $o = array();
    foreach ($r as $r) {

      switch ($r['status']){
      case 0: $r['state']='<span class="label label-danger">No</span>'; $r['state_class']='danger'; break;
      case 1: $r['state']='<span class="label label-success">Yes</span>'; $r['state_class']=''; break;
      }

      switch ($r['admin']){
      case 0: $r['is-admin']='<span class="label label-danger">No</span>'; break;
      case 1: $r['is-admin']='<span class="label label-success">Yes</span>'; break;
      }

      $o[]=$r;
    }
    return $o;
  }
}

function allServersTabe(){
  $q = "select * from servers";
  $r = mql($q);

  echo createTableFromArray($r,

    /* $ignoredHeaders */
    array(
      'id',
'sys_cap',
'devices',
'mounts',
      'full',
    ),

    /* $renamedHeaders */
    array(
      'extend_days' => 'Extend days (Editable)',
      'principals' => 'Principals (Editable)',
    ),

    array(
    )
  );
}
