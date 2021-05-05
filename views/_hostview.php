<?php
$id = (int) $u[1];
$r = mql("select * from servers where id=$id limit 1");
if(empty($r)){
  die();
}
foreach($r as $k=>$v){
  ${"$k"} = $v;
}
?>
<div class="row align-items-start host_details">
  <div class="col">
    <p class='atr'>Fqdn: <span class="badge bg-primary"><?php echo $fqdn; ?></span></p>
    <p class='atr'>Hostname: <span><?php echo $hostname; ?></span></p>
    <p class='atr'>Nodename: <span><?php echo $nodename; ?></span></p>
    <p class='atr'>Uuid: <span><?php echo $uuid; ?></span></p>
    <hr>
    <p class='atr'>Distro release: <span><?php echo $distro; ?> <?php echo $distro_ver; ?> (<?php echo $distro_release; ?>)</span> / Os family: <span><?php echo $os_family; ?></span></p>
    <hr>
    <p class='atr'>Kernel: <span><?php echo $kernel; ?></span></p>
    <p class='atr'>Product name: <span><?php echo $product_name; ?></span></p>
    <p class='atr'>Product serial: <span><?php echo $product_serial; ?></span></p>
    <p class='atr'>Product ver: <span><?php echo $product_ver; ?></span></p>
    <p class='atr'>Arch: <span><?php echo $arch; ?></span></p>
    <hr>
    <p class='atr'>Sys vendor: <span><?php echo $sys_vendor; ?></span></p>
    <p class='atr'>Virt type: <span><?php echo $virt_type; ?></span></p>
    <p class='atr'>Virt role: <span><?php echo $virt_role; ?></span></p>
    <p class='atr'>Uptime sec: <span><?php echo $uptime_sec; ?></span></p>
    <p class='atr'>Is chroot: <span><?php echo $is_chroot; ?></span></p>
    <p class='atr'>Iscsi iqn: <span><?php echo $iscsi_iqn; ?></span></p>
    <p class='atr'>Service mgr: <span><?php echo $service_mgr; ?></span></p>
    <hr>
    <p class='atr'>Bios ver: <span><?php echo $bios_ver; ?></span></p>
    <p class='atr'>Bios date: <span><?php echo $bios_date; ?></span></p>
    <p class='atr'>Boot image: <span><?php echo $boot_image; ?></span></p>
  </div>
  <div class="col">
    <p class='atr'>Cpu cores: <span><?php echo $cpu_cores; ?></span></p>
    <p class='atr'>Cpu count: <span><?php echo $cpu_count; ?></span></p>
    <p class='atr'>Cpu threads per core: <span><?php echo $cpu_threads_per_core; ?></span></p>
    <p class='atr'>Cpu vcpus: <span><?php echo $cpu_vcpus; ?></span></p>
    <hr>
    <p class='atr'>Selinux: <span><?php echo $selinux_status; ?> / <?php echo $selinux_mode; ?> / <?php echo $selinux_type; ?></span></p>
    <hr>
    <p class='atr'>All ipsv4: <span><?php echo $all_ipsv4; ?></span></p>
    <p class='atr'>All ipsv6: <span><?php echo $all_ipsv6; ?></span></p>
    <p class='atr'>Main ip address: <span><?php echo $main_ip_address; ?></span></p>
    <p class='atr'>Main ip netmask: <span><?php echo $main_ip_netmask; ?></span></p>
    <p class='atr'>Main ip gateway: <span><?php echo $main_ip_gateway; ?></span></p>
    <p class='atr'>Main ip interface: <span><?php echo $main_ip_interface; ?></span></p>
    <p class='atr'>Main ip mac: <span><?php echo $main_ip_mac; ?></span></p>
    <p class='atr'>Main ip network: <span><?php echo $main_ip_network; ?></span></p>
    <p class='atr'>Main ip type: <span><?php echo $main_ip_type; ?></span></p>
    <p class='atr'>Domain: <span><?php echo $domain; ?></span></p>
    <p class='atr'>Dns ns: <span><?php echo $dns_ns; ?></span></p>
    <hr>
    <p class='atr'>Memory free: <span><?php echo $memory_free; ?></span> / Memory total: <span><?php echo $memory_total; ?></span>
      <!--div class="progress" style="height:10px;"> <div class="progress-bar progress-bar-striped bg-danger" role="progressbar" style="width: <?php echo ($memory_total / ($memory_total - $memory_free)); ?>%" aria-valuenow="<?php echo ($memory_total - $memory_free); ?>" aria-valuemin="0" aria-valuemax="<?php echo $memory_total; ?>"></div> </div-->
      <div class="progress"> <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: <?php echo (100 / ($memory_total / $memory_free)); ?>%" aria-valuenow="<?php echo (100 / ($memory_total / $memory_free)); ?>" aria-valuemin="0" aria-valuemax="<?php echo $memory_total; ?>"></div><?php echo (int) (100 / ($memory_total / $memory_free)); ?>%</div>
    </p>
    <p class='atr'>Memory swap free: <span><?php echo $memory_swap_free; ?></span> / Memory swap total: <span><?php echo $memory_swap_total; ?></span>
      <!--div class="progress" style="height:10px;"> <div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: <?php echo ($memory_swap_total / ($memory_swap_total - $memory_swap_free)); ?>%" aria-valuenow="<?php echo ($memory_swap_total - $memory_swap_free); ?>" aria-valuemin="0" aria-valuemax="<?php echo $memory_swap_total; ?>"></div> </div-->
      <div class="progress"> <div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: <?php echo (100 / ($memory_swap_total / $memory_swap_free)); ?>%" aria-valuenow="<?php echo (100 / ($memory_swap_total / $memory_swap_free)); ?>" aria-valuemin="0" aria-valuemax="<?php echo $memory_swap_total; ?>"><?php echo (int) (100 / ($memory_swap_total / $memory_swap_free)); ?>%</div> </div>
    </p>
  </div>
  <div class="col">
    <p class='atr'>System capabilities enforced: <span><?php echo $system_capabilities_enforced; ?></span></p>
    <p class='atr'>Sys cap: <span><?php echo $sys_cap; ?></span></p>
    <p class='atr'>Epoch time: <span><?php echo $epoch_time; ?></span></p>
    <p class='atr'>Devices:<br> <span><?php echo $devices; ?></span></p>
    <hr>
    <p class='atr'>Mounts:<br> <span><?php echo $mounts; ?></span></p>
    <hr>
    <p class='atr'>Lvm:<br> <span><?php echo $lvm; ?></span></p>
    <hr>
    <p class='atr'>Time of data recorderd: <span><?php echo $ts; ?></span></p>
    <p class='atr'>Python ver: <span><?php echo $python_ver; ?></span></p>
  </div>
</div>
