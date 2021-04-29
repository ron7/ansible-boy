<?php
/* use PHPMailer\PHPMailer\PHPMailer; */
/* use PHPMailer\PHPMailer\Exception; */

date_default_timezone_set('Europe/Sofia');
setlocale(LC_ALL, 'bg_BG');

$minify_html=0;
$debug=0;
$debugIps= array('127.0.0.1');
$sitename = 'X'; //default, overwritten by .env

$defaulthashes = array('fqdn','main_ip_address','main_ip_gateway');

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

function resetDbTableServers(){
  $q = "drop table if exists servers;";
  @mql($q);
  $q = "
CREATE TABLE `servers` (
`id` INT NOT NULL AUTO_INCREMENT ,
`uuid` VARCHAR(40) NOT NULL ,
`os_family` VARCHAR(20) NULL ,
`distro` VARCHAR(40) NULL ,
`distro_release` VARCHAR(40) NULL ,
`distro_mver` INT(5) NULL ,
`distro_ver` VARCHAR(10) NULL ,
`kernel` VARCHAR(255) NULL ,
`product_name` VARCHAR(100) NULL ,
`product_serial` VARCHAR(100) NULL ,
`product_ver` VARCHAR(30) NULL ,
`arch` VARCHAR(10) NULL ,
`sys_vendor` VARCHAR(60) NULL ,
`virt_type` VARCHAR(60) NULL ,
`virt_role` VARCHAR(60) NULL ,
`uptime_sec` VARCHAR(20) NULL ,
`fqdn` VARCHAR(70) NULL ,
`hostname` VARCHAR(70) NULL ,
`nodename` VARCHAR(70) NULL ,
`is_chroot` VARCHAR(10) NULL ,
`iscsi_iqn` VARCHAR(100) NULL ,
`cpu_cores` VARCHAR(4) NULL ,
`cpu_count` VARCHAR(4) NULL ,
`cpu_threads_per_core` VARCHAR(4) NULL ,
`cpu_vcpus` VARCHAR(4) NULL ,
`system_capabilities_enforced` VARCHAR(1) NULL ,
`epoch_time` VARCHAR(50) NULL ,
`bios_ver` VARCHAR(20) NULL ,
`bios_date` VARCHAR(20) NULL ,
`boot_image` VARCHAR(255) NULL ,
`selinux_status` VARCHAR(50) NULL ,
`selinux_mode` VARCHAR(20) NULL ,
`selinux_type` VARCHAR(20) NULL ,
`service_mgr` VARCHAR(20) NULL ,
`python_ver` VARCHAR(10) NULL ,
`all_ipsv4` TEXT NULL ,
`all_ipsv6` TEXT NULL ,
main_ip_address VARCHAR(20) NULL ,
main_ip_netmask VARCHAR(20) NULL ,
main_ip_gateway VARCHAR(20) NULL ,
main_ip_interface VARCHAR(20) NULL ,
main_ip_mac VARCHAR(20) NULL ,
main_ip_network VARCHAR(20) NULL ,
main_ip_type VARCHAR(20) NULL ,
`domain` VARCHAR(100) NULL ,
`dns_ns` VARCHAR(255) NULL ,
`sys_cap` TEXT NULL ,
`memory_free` INT(10) NULL ,
`memory_total` INT(10) NULL ,
`memory_swap_free` INT(10) NULL ,
`memory_swap_total` INT(10) NULL ,
`devices` TEXT NULL ,
`mounts` TEXT NULL ,
`lvm` TEXT NULL ,
`ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
`state` TEXT NULL ,
`full` JSON NULL ,
PRIMARY KEY (`id`), UNIQUE KEY `uuid` (`uuid`)) ENGINE = InnoDB;
";
  @mql($q);
}
