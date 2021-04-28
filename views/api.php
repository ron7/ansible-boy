<?php
if(($_POST['table']=='main') and (!empty($_POST['tags']))){
  $tags = array_filter(explode("|",clean(urldecode($_POST['tags']),'\|\_')));
  $_SESSION['tagsArr'] = $tags;
  $tags_sql = implode(',',$tags);
  $_SESSION['tagsSql'] = $tags_sql;
  /* print_r($tags_sql); */
}

/* $w = mql('show columns from servers'); */
/* print_r($w); */
$columns = array_values($_SESSION['tagsArr']);
$sql = $_SESSION['tagsSql'];
/* $sql = str_replace(',main_ip,',',JSON_EXTRACT(main_ip,"$.address") as main_ip_addr,JSON_EXTRACT(main_ip,"$.gateway") as main_ip_gw,',$sql); */
$sql = str_replace(',main_ip,',',JSON_EXTRACT(main_ip,"$.address") as main_ip,',$sql);
$q = "select {$sql} from servers";
/* echo $q; */
/* die(); */
$r = mql($q);
echo json_encode(array( 'columns' => $columns, 'data' => $r),JSON_UNESCAPED_UNICODE);
die();

