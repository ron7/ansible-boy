<?php
$folder=__DIR__.'/.facts/';
require_once __DIR__.'/global_functions.php';
$aFiles = array();
if ($handle = opendir($folder)) {
  while (false !== ($entry = readdir($handle))) {
    if ($entry != "." && $entry != "..") {
      /* echo "$entry\n"; */
      $aFiles[] = $entry;
    }
  }
  closedir($handle);
}

function humanUptimeFromSeconds($sec) {
  /* $str   = @file_get_contents('/proc/uptime'); */
  /* $sec   = floatval($str); */
  $secs  = $sec % 60;
  $sec   = (int)($sec / 60);
  $mins  = $sec % 60;
  $sec   = (int)($sec / 60);
  $hours = $sec % 24;
  $sec   = (int)($sec / 24);
  $days  = $sec;

  $ut = array(
    "days"  => $days,
    "hours" => $hours,
    "mins"  => $mins,
    "secs"  => $secs
  );
  return "{$ut['days']}d {$ut['hours']}h {$ut['mins']}m";
}

sort($aFiles);
/* print_r($aFiles); */

if(!empty($aFiles)){
  foreach($aFiles as $file){
    $f = file_get_contents($folder.$file);
    $af = json_decode( $f );

    if($af->rc > 0){
      /* $uuid = 's3_'.md5($file); */
      /* $q = "insert into servers ( */
      /*   id, */
      /*   uuid, */
      /*   fqdn, */
      /*   hostname, */
      /* state */
      /* ) VALUES ( */

      /* 0, */
      /* '$uuid', */
      /* '{$file}', */
      /* '{$file}', */
      /* 'Failed: rc: {$af->rc}' */
      /* )"; */
      /* mql($q); */

      echo "Error for $file, RC: {$af->rc}\n";

      continue;
    }

    if(empty($af->ansible_facts)){

      echo "Error for $file No Facts found: $f\n";
      /* $uuid = md5($file); */
      /* $q = "insert into servers ( */
      /*   id, */
      /*   uuid, */
      /*   fqdn, */
      /*   hostname, */
      /* state */
      /* ) VALUES ( */

      /* 0, */
      /* '$uuid', */
      /* '{$file}', */
      /* '{$file}', */
      /* 'Failed: {$af->msg}' */
      /* )"; */
      /* mql($q); */

    }else{
      unset($af->ansible_facts->ansible_env);
      unset($af->ansible_facts->gather_subset);
      /* print_r($af->ansible_facts); */

      $uuid = md5($file);
/*
      $uuid = $af->ansible_facts->ansible_product_uuid;

      if(strlen($uuid) < 5){
        $uuid = 's2_'.$af->ansible_facts->ansible_machine_id;
      }

      if(strlen($uuid) < 5){
        $uuid = 's3_'.md5($file);
      }
 */
      $allIpsv4 = implode(',',$af->ansible_facts->ansible_all_ipv4_addresses);
      $allIpsv6 = implode(',',$af->ansible_facts->ansible_all_ipv6_addresses);
      $dns_ns = implode(',',$af->ansible_facts->ansible_dns->nameservers);
      $sys_cap = implode(',',$af->ansible_facts->ansible_system_capabilities);

      $devices = '';
      foreach($af->ansible_facts->ansible_devices as $k=>$device){
        $devices .= "<span class=\"badge bg-secondary\">$k</span> Size: <span class=\"badge bg-success\">{$device->size}</span> Model: <span class=\"badge bg-info\">{$device->model}</span> Vendor: <span class=\"badge bg-danger\">{$device->vendor}</span> Host: <span class=\"badge bg-secondary\">{$device->host}</span>\n";

      }
      $devices = nl2br($devices);

      $mounts = '';
      if(!empty($af->ansible_facts->ansible_mounts)){
        foreach($af->ansible_facts->ansible_mounts as $mnt){
          $mounts .= "UUID=<span class=\"badge bg-secondary\">{$mnt->uuid}</span> <span class=\"badge bg-danger\">{$mnt->mount}</span> <span class=\"badge bg-info\">{$mnt->fstype}</span> <span class=\"badge bg-success\">{$mnt->options}</span> (Device: {$mnt->device} Size: {$mnt->size_total}, Blocks: {$mnt->block_used} used out of {$mnt->block_total} total, Inodes: {$mnt->inode_used} used out of {$mnt->inode_total} total)\n";
        }
      }
      $mounts = nl2br($mounts);
      $lvm = '';
      if(!empty($af->ansible_facts->ansible_lvm)){
        foreach($af->ansible_facts->ansible_lvm as $k=>$lvmc){
          foreach($lvmc as $kk=>$lvmcc){
            $lvm .= "<span class=\"badge bg-secondary\">$k</span> <span class=\"badge bg-danger\">$kk:</span> Size: <span class=\"badge bg-success\">{$lvmcc->size_g} Gb</span> <span class=\"badge bg-info\">Free: {$lvmcc->free_g} Gb</span> <span class=\"badge bg-secondary\">Vg: {$lvmcc->vg}</span>";
            if($k == 'vgs'){
              $lvm .= " <span class=\"badge bg-secondary\">Num lvs: {$lvmcc->num_lvs}</span> <span class=\"badge bg-secondary\">Num pvs: {$lvmcc->num_pvs}</span>";
            }
            $lvm.="\n";
          }
        }
      }
      $lvm = nl2br($lvm);
      $full = json_encode($af->ansible_facts);

      $system_capabilities_enforced = null;
      if( $af->ansible_facts->ansible_system_capabilities_enforced == 'True'){
        $system_capabilities_enforced = 1;
      }elseif( $af->ansible_facts->ansible_system_capabilities_enforced == 'False'){
        $system_capabilities_enforced = 0;
      }
      $uptime_human = humanUptimeFromSeconds($af->ansible_facts->ansible_uptime_seconds);
      $epoch_human = date('r',$af->ansible_facts->ansible_date_time->epoch);

      $q = "insert into servers (
        id,
        uuid,
        os_family,
        distro,
        distro_release,
        distro_mver,
        distro_ver,
        kernel,
        product_name,
        product_serial,
        product_ver,
        arch,
        sys_vendor,
        virt_type,
        virt_role,
        uptime_sec,
        fqdn,
        hostname,
        nodename,
        is_chroot,
        iscsi_iqn,
        cpu_cores,
        cpu_count,
        cpu_threads_per_core,
        cpu_vcpus,
        system_capabilities_enforced,
        epoch_time,
        bios_ver,
        bios_date,
        boot_image,
        selinux_status,
        selinux_mode,
        selinux_type,
        service_mgr,
        python_ver,
        all_ipsv4,
        all_ipsv6,
        main_ip_address,
        main_ip_netmask,
        main_ip_gateway,
        main_ip_interface,
        main_ip_mac,
        main_ip_network,
        main_ip_type,
        domain,
        dns_ns,
        sys_cap,
        memory_free,
        memory_total,
        memory_swap_free,
        memory_swap_total,
        devices,
        mounts,
        lvm,
full


) values (
  0,
  '$uuid',
  '{$af->ansible_facts->ansible_os_family}',
  '{$af->ansible_facts->ansible_distribution}',
  '{$af->ansible_facts->ansible_distribution_release}',
  {$af->ansible_facts->ansible_distribution_major_version},
  '{$af->ansible_facts->ansible_distribution_version}',
  '{$af->ansible_facts->ansible_kernel};{$af->ansible_facts->ansible_kernel_version}',
  '{$af->ansible_facts->ansible_product_name}',
  '{$af->ansible_facts->ansible_product_serial}',
  '{$af->ansible_facts->ansible_product_version}',
  '{$af->ansible_facts->ansible_architecture}',
  '{$af->ansible_facts->ansible_system_vendor}',
  '{$af->ansible_facts->ansible_virtualization_type}',
  '{$af->ansible_facts->ansible_virtualization_role}',
  '$uptime_human',
  '{$af->ansible_facts->ansible_fqdn}',
  '{$af->ansible_facts->ansible_hostname}',
  '{$af->ansible_facts->ansible_nodename}',
  '{$af->ansible_facts->ansible_is_chroot}',
  '{$af->ansible_facts->ansible_iscsi_iqn}',
  '{$af->ansible_facts->ansible_processor_cores}',
  '{$af->ansible_facts->ansible_processor_count}',
  '{$af->ansible_facts->ansible_processor_threads_per_core}',
  '{$af->ansible_facts->ansible_processor_vcpus}',
  '{$system_capabilities_enforced}',
  '$epoch_human',
  '{$af->ansible_facts->ansible_bios_version}',
  '{$af->ansible_facts->ansible_bios_date}',
  '{$af->ansible_facts->ansible_cmdline->BOOT_IMAGE}',
  '{$af->ansible_facts->ansible_selinux->status}',
  '{$af->ansible_facts->ansible_selinux->mode}',
  '{$af->ansible_facts->ansible_selinux->type}',
  '{$af->ansible_facts->ansible_service_mgr}',
  '{$af->ansible_facts->ansible_python_version}',
  '{$allIpsv4}',
  '{$allIpsv6}',
  '{$af->ansible_facts->ansible_default_ipv4->address}',
  '{$af->ansible_facts->ansible_default_ipv4->netmask}',
  '{$af->ansible_facts->ansible_default_ipv4->gateway}',
  '{$af->ansible_facts->ansible_default_ipv4->interface}',
  '{$af->ansible_facts->ansible_default_ipv4->macaddress}',
  '{$af->ansible_facts->ansible_default_ipv4->network}',
  '{$af->ansible_facts->ansible_default_ipv4->type}',
  '{$af->ansible_facts->ansible_domain}',
  '$dns_ns',
  '$sys_cap',
  {$af->ansible_facts->ansible_memfree_mb},
  {$af->ansible_facts->ansible_memtotal_mb},
  {$af->ansible_facts->ansible_swapfree_mb},
  {$af->ansible_facts->ansible_swaptotal_mb},
  '$devices',
  '$mounts',
  '$lvm',
  '$full'

) ON DUPLICATE KEY UPDATE
os_family = '{$af->ansible_facts->ansible_os_family}',
distro = '{$af->ansible_facts->ansible_distribution}',
distro_release = '{$af->ansible_facts->ansible_distribution_release}',
distro_mver = {$af->ansible_facts->ansible_distribution_major_version},
distro_ver = '{$af->ansible_facts->ansible_distribution_version}',
kernel = '{$af->ansible_facts->ansible_kernel};{$af->ansible_facts->ansible_kernel_version}',
product_name = '{$af->ansible_facts->ansible_product_name}',
product_serial = '{$af->ansible_facts->ansible_product_serial}',
product_ver = '{$af->ansible_facts->ansible_product_version}',
arch = '{$af->ansible_facts->ansible_architecture}',
sys_vendor = '{$af->ansible_facts->ansible_system_vendor}',
virt_type = '{$af->ansible_facts->ansible_virtualization_type}',
virt_role = '{$af->ansible_facts->ansible_virtualization_role}',
uptime_sec = '$uptime_human',
fqdn = '{$af->ansible_facts->ansible_fqdn}',
hostname = '{$af->ansible_facts->ansible_hostname}',
nodename = '{$af->ansible_facts->ansible_nodename}',
is_chroot = '{$af->ansible_facts->ansible_is_chroot}',
iscsi_iqn = '{$af->ansible_facts->ansible_iscsi_iqn}',
cpu_cores = '{$af->ansible_facts->ansible_processor_cores}',
cpu_count = '{$af->ansible_facts->ansible_processor_count}',
cpu_threads_per_core = '{$af->ansible_facts->ansible_processor_threads_per_core}',
cpu_vcpus = '{$af->ansible_facts->ansible_processor_vcpus}',
system_capabilities_enforced = '{$system_capabilities_enforced}',
epoch_time = '$epoch_human',
bios_ver = '{$af->ansible_facts->ansible_bios_version}',
bios_date = '{$af->ansible_facts->ansible_bios_date}',
boot_image = '{$af->ansible_facts->ansible_cmdline->BOOT_IMAGE}',
selinux_status = '{$af->ansible_facts->ansible_selinux->status}',
selinux_mode = '{$af->ansible_facts->ansible_selinux->mode}',
selinux_type =  '{$af->ansible_facts->ansible_selinux->type}',
service_mgr = '{$af->ansible_facts->ansible_service_mgr}',
python_ver = '{$af->ansible_facts->ansible_python_version}',
all_ipsv4 = '{$allIpsv4}',
all_ipsv6 = '{$allIpsv6}',
main_ip_address =  '{$af->ansible_facts->ansible_default_ipv4->address}',
main_ip_netmask =  '{$af->ansible_facts->ansible_default_ipv4->netmask}',
main_ip_gateway =  '{$af->ansible_facts->ansible_default_ipv4->gateway}',
main_ip_interface =  '{$af->ansible_facts->ansible_default_ipv4->interface}',
main_ip_mac =  '{$af->ansible_facts->ansible_default_ipv4->macaddress}',
main_ip_network =  '{$af->ansible_facts->ansible_default_ipv4->network}',
main_ip_type =  '{$af->ansible_facts->ansible_default_ipv4->type}',
domain = '{$af->ansible_facts->ansible_domain}',
dns_ns = '$dns_ns',
sys_cap = '$sys_cap',
memory_free = {$af->ansible_facts->ansible_memfree_mb},
memory_total = {$af->ansible_facts->ansible_memtotal_mb},
memory_swap_free = {$af->ansible_facts->ansible_swapfree_mb},
memory_swap_total = {$af->ansible_facts->ansible_swaptotal_mb},
devices = '$devices',
mounts = '$mounts',
lvm = '$lvm',
full = '$full',
ts = NOW()

";
      /* echo $q; */
      mql($q);
      echo ":: Inserted $file\n";


      /* $rDomain = array('','xron.net'); */
      /* $rDom = array_rand($rDomain, 1); */
      /* $rD=$rDomain[$rDom]; */
      /* $rIp = '10.200.'.rand(1,250).'.'.rand(1,250); */

      /* $rHost = generateRandomString(); */
      /* $rFqdn = "{$rHost}.xron.net"; */

      /* $q = "update servers set domain='$rD',main_ip_address='$rIp',hostname='host-$rHost',nodename='host-$rHost',fqdn='$rFqdn' where uuid='$uuid'"; */
      /* echo "\n\n$q\n\n"; */
      /* mql($q); */

    }
  }


}else{
  die("Error: aFiles is empty\n");
}

