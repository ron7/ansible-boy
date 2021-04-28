<?php
$folder=__DIR__.'/facts/';
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

/* print_r($aFiles); */
if(!empty($aFiles)){
  foreach($aFiles as $file){
    $f = file_get_contents($folder.$file);
    $af = json_decode( $f );
    if(!empty($af->ansible_facts)){
      unset($af->ansible_facts->ansible_env);
      unset($af->ansible_facts->gather_subset);
      /* print_r($af->ansible_facts); */

      $uuid = $af->ansible_facts->ansible_product_uuid;
      if(strlen($uuid) < 5){
        $uuid = 's2_'.$af->ansible_facts->ansible_machine_id;
      }
      if(strlen($uuid) < 5){
        $uuid = 's3_'.md5($file);
        /* $firstObjVal = reset($af->ansible_facts->ansible_device_links->uuids); */
        /* $uuid = $firstObjVal[0]; */
      }

      $allIpsv4 = implode(',',$af->ansible_facts->ansible_all_ipv4_addresses);
      $allIpsv6 = implode(',',$af->ansible_facts->ansible_all_ipv6_addresses);
      $dns_ns = implode(',',$af->ansible_facts->ansible_dns->nameservers);
      $sys_cap = implode(',',$af->ansible_facts->ansible_system_capabilities);

      $devices = array();
      foreach($af->ansible_facts->ansible_devices as $k=>$device){
        $devices[$k]['size'] = $device->size;
        $devices[$k]['uuid'] = $device->links->uuids[0];
        $devices[$k]['host'] = $device->host;
        $devices[$k]['model'] = $device->model;
        $devices[$k]['vendor'] = $device->vendor;

      }
      $devices = json_encode($devices);

      $mounts = json_encode($af->ansible_facts->ansible_mounts);
      $lvm = json_encode($af->ansible_facts->ansible_lvm);
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
      echo "Inserting: {$uuid} / {$af->ansible_facts->ansible_fqdn} ({$af->ansible_facts->ansible_hostname}) = {$af->ansible_facts->ansible_distribution} {$af->ansible_facts->ansible_distribution_release} {$af->ansible_facts->ansible_distribution_version} {$af->ansible_facts->ansible_system_vendor} ({$af->ansible_facts->ansible_virtualization_type}) = {$af->ansible_facts->ansible_default_ipv4->address} = {$af->ansible_facts->ansible_selinux->status}\n";

/*
drop table if exists servers;
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
`devices` JSON NULL ,
`mounts` JSON NULL ,
`lvm` JSON NULL ,
`ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
`full` JSON NULL ,
PRIMARY KEY (`id`), UNIQUE KEY `uuid` (`uuid`)) ENGINE = InnoDB;

 */




/*
-//device links
.//devices
//mounts
//lvm?

.    [ansible_memfree_mb] => 129
-    [ansible_memory_mb] => stdClass Object
.        (
.            [nocache] => stdClass Object
.                (
.                    [free] => 3478
.                    [used] => 3743
.                )
.
.            [real] => stdClass Object
.                (
.                    [free] => 129
.                    [total] => 7221
.                    [used] => 7092
.                )
.
.            [swap] => stdClass Object
.                (
.                    [cached] => 22
.                    [free] => 1999
.                    [total] => 2047
.                    [used] => 48
.                )
.
.        )
.
.    [ansible_memtotal_mb] => 7221
.
.    [ansible_swapfree_mb] => 1999
.    [ansible_swaptotal_mb] => 2047


.[ansible_system_capabilities] => Array
.        (
.            [0] => cap_chown
.            [1] => cap_dac_override
.            [2] => cap_dac_read_search
.            [3] => cap_fowner
.            [4] => cap_fsetid
.            [5] => cap_kill
.            [6] => cap_setgid
.            [7] => cap_setuid
.            [8] => cap_setpcap

.    [ansible_dns] => stdClass Object
.        (
.            [nameservers] => Array
.                (
.                    [0] => 10.48.5.210
.                    [1] => 1.1.1.1
.                    [2] => 8.8.8.8
.                )
.
.            [options] => stdClass Object
.                (
-                    [timeout] => 1
.                )
.
.            [search] => Array
.                (
-                    [0] => codero.com
.                )
.
.        )
.
.    [ansible_domain] =>


.    [ansible_python_version] => 2.7.5
-    [ansible_real_group_id] => 0
-    [ansible_real_user_id] => 0
.    [ansible_selinux] => stdClass Object
.        (
.            [config_mode] => permissive
.            [mode] => permissive
.            [policyvers] => 31
.            [status] => enabled
.            [type] => targeted
.        )
.
-    [ansible_selinux_python_present] => 1
.    [ansible_service_mgr] => systemd





.    [ansible_all_ipv4_addresses] => Array
.        (
.            [0] => 10.48.5.73
.        )
.
.    [ansible_all_ipv6_addresses] => Array
.        (
.            [0] => fe80::250:56ff:fea1:8c0f
.        )

    [ansible_apparmor] => stdClass Object
        (
            [status] => disabled
        )

.    [ansible_architecture] => x86_64
.    [ansible_bios_date] => 04/05/2016
.    [ansible_bios_version] => 6.00
.    [ansible_cmdline] => stdClass Object
.        (
.            [BOOT_IMAGE] => /vmlinuz-3.10.0-1127.19.1.el7.x86_64
.            [LANG] => en_US.UTF-8
.            [biosdevname] => 0
.            [crashkernel] => auto
.            [net.ifnames] => 0
.            [quiet] => 1
.            [rhgb] => 1
.            [ro] => 1
.            [root] => /dev/centos/root
.        )

-    [ansible_date_time] => stdClass Object
-        (
-            [date] => 2021-04-23
-            [day] => 23
.            [epoch] => 1619231158
-            [hour] => 22
-            [iso8601] => 2021-04-24T02:25:58Z
-            [iso8601_basic] => 20210423T222558808442
-            [iso8601_basic_short] => 20210423T222558
-            [iso8601_micro] => 2021-04-24T02:25:58.808888Z
-            [minute] => 25
-            [month] => 04
-            [second] => 58
-            [time] => 22:25:58
-            [tz] => EDT
-            [tz_offset] => -0400
-            [weekday] => Friday
-            [weekday_number] => 5
-            [weeknumber] => 16
-            [year] => 2021
-        )
-
.    [ansible_default_ipv4] => stdClass Object
.        (
.            [address] => 10.48.5.73
.            [alias] => eth0
.            [broadcast] => 10.48.5.255
.            [gateway] => 10.48.5.1
.            [interface] => eth0
.            [macaddress] => 00:50:56:a1:8c:0f
.            [mtu] => 1500
.            [netmask] => 255.255.255.0
.            [network] => 10.48.5.0
.            [type] => ether
.        )



.    [ansible_distribution] => CentOS
-    [ansible_distribution_file_parsed] => 1
-    [ansible_distribution_file_path] => /etc/redhat-release
-    [ansible_distribution_file_variety] => RedHat
.    [ansible_distribution_major_version] => 7
.    [ansible_distribution_release] => Core
.    [ansible_distribution_version] => 7.8

-    [ansible_fips] =>
-    [ansible_form_factor] => Other
.    [ansible_fqdn] => phx1-zabbix3
.    [ansible_hostname] => phx1-zabbix3
-    [ansible_hostnqn] =>
    [ansible_interfaces] => Array
        (
            [0] => lo
            [1] => eth0
        )

.    [ansible_is_chroot] =>
.    [ansible_iscsi_iqn] =>
.    [ansible_kernel] => 3.10.0-1127.19.1.el7.x86_64
.    [ansible_kernel_version] => #1 SMP Tue Aug 25 17:23:54 UTC 2020

.    [ansible_nodename] => phx1-zabbix3
.    [ansible_os_family] => RedHat

.    [ansible_processor_cores] => 1
.    [ansible_processor_count] => 4
.    [ansible_processor_threads_per_core] => 1
.    [ansible_processor_vcpus] => 4
.    [ansible_product_name] => VMware Virtual Platform
.    [ansible_product_serial] => VMware-42 21 be 8a df dd 66 37-cd 0b 75 1b 4a 36 0e 37
.    [ansible_product_uuid] => 8ABE2142-DDDF-3766-CD0B-751B4A360E37
.    [ansible_product_version] => None

.    [ansible_system_capabilities_enforced] => True
.    [ansible_system_vendor] => VMware, Inc.
.    [ansible_uptime_seconds] => 14532071
-    [ansible_user_dir] => /root
-    [ansible_user_gecos] => root
-    [ansible_user_gid] => 0
-    [ansible_user_id] => root
-    [ansible_user_shell] => /bin/bash
-    [ansible_user_uid] => 0
-    [ansible_userspace_architecture] => x86_64
-    [ansible_userspace_bits] => 64
.    [ansible_virtualization_role] => guest
.    [ansible_virtualization_type] => VMware
 */

    }else{
      echo ":: $file has no facts\n";
    }
  }


}else{
  die("Error: aFiles is empty\n");
}

