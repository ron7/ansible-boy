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
$q = "select {$sql} from servers";
$r = mql($q);

echo json_encode(array( 'columns' => $columns, 'data' => $r),JSON_UNESCAPED_UNICODE);
die();

