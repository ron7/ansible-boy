<?php
if(($_GET['download']== 'csv') && ($_GET['data']<> '')){
  dldCsv($_GET['data']);
  die();
}
if($_POST['action']=== 'resetDbTableServers'){
  resetDbTableServers();
  die();
}
if(($_POST['table']=='main') and (!empty($_POST['tags']))){
  $tags = array_filter(explode("|",clean(urldecode($_POST['tags']),'\|\_')));
  $_SESSION['tagsArr'] = $tags;
  $tags_sql = implode(',',$tags);
  $_SESSION['tagsSql'] = $tags_sql;
}

$columns = array_values($_SESSION['tagsArr']);
$sql = $_SESSION['tagsSql'];
$q = "select id,fqdn as system_fqdn,{$sql} from servers";
$r = mql($q);
$aR = array();
foreach($r as $k=>$v){
  foreach($v as $l=>$w){
    if(in_array($l,array('fqdn','hostname','nodename','main_ip_address'))){
      $aR[$k][$l] = "<a href='/host/{$v['id']}/{$v['system_fqdn']}' target='_blank'>$w</a>";
    }else{
      $aR[$k][$l] = $w;
    }
    unset($aR[$k]['id']);
    unset($aR[$k]['system_fqdn']);
  }
}

echo json_encode(array( 'count' => count($aR), 'columns' => $columns, 'data' => $aR),JSON_UNESCAPED_UNICODE);
die();

