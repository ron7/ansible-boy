<?php
if(($_POST['table']=='main') and (!empty($_POST['tags']))){
  $tags = array_filter(explode("|",clean(urldecode($_POST['tags']),'\|\_')));
  $_SESSION['tagsArr'] = $tags;
  $tags_sql = implode(',',$tags);
  $_SESSION['tagsSql'] = $tags_sql;
  /* print_r($tags_sql); */
}

function Uptime($num) {
        /* $str   = @file_get_contents('/proc/uptime'); */
        /* $num   = floatval($str); */
        $secs  = $num % 60;
        $num   = (int)($num / 60);
        $mins  = $num % 60;
        $num   = (int)($num / 60);
        $hours = $num % 24;
        $num   = (int)($num / 24);
        $days  = $num;

        return array(
            "days"  => $days,
            "hours" => $hours,
            "mins"  => $mins,
            "secs"  => $secs
        );
    }


/* $w = mql('show columns from servers'); */
/* print_r($w); */
$columns = array_values($_SESSION['tagsArr']);
$sql = $_SESSION['tagsSql'];
/* $sql = str_replace(',main_ip,',',JSON_EXTRACT(main_ip,"$.address") as main_ip_addr,JSON_EXTRACT(main_ip,"$.gateway") as main_ip_gw,',$sql); */
/* $sql = str_replace(',main_ip,',',JSON_EXTRACT(main_ip,"$.address") as main_ip,',$sql); */
$q = "select {$sql} from servers";
/* echo $q; */
/* die(); */
$r = mql($q);

foreach($r as $r2){
$ut = Uptime($r2['uptime_sec']);
$tt = "Uptime: {$ut['days']}d {$ut['hours']}h {$ut['mins']}m";
//echo "r: {$r2['uptime_sec']}, time: $tt\n";
}
echo json_encode(array( 'columns' => $columns, 'data' => $r),JSON_UNESCAPED_UNICODE);
die();

