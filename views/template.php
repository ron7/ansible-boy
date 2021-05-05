<?php
header('Content-type: text/html; charset=utf-8');
ob_start("mini");
if (empty($_SESSION['username'])) { // NOT loggedin
  include("login.php");
}elseif($u[0]==='api'){
  header('Content-type: application/json; charset=utf-8');
  include('api.php');
}else{
  include('header.php');
  $page = "./".$baseUrl."/".clean($u[0],"\_\.\-").".php";
  if(($u[0]==='host') && ($u['1']<>'')){
    include('_hostview.php');
  }elseif(($u[0]<>"") AND (file_exists($page))){
    include($page);
  }else{
    include('mainpage.php');
  }
  include('footer.php');
}
ob_end_flush();
