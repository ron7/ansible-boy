<?php
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

