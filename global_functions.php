<?php

ini_set('filter.default', 'full_special_chars');
ini_set('filter.default_flags', 0);
ini_set('session.gc_maxlifetime', '31536000'); // 31536000= 1y
ini_set('session.cache_expire', 31536000);
ini_set('session.cookie_lifetime', 31536000);

if (file_exists(__DIR__."/conf.php")) {
  include __DIR__."/conf.php";
}

$rel_path = substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT']));
$baseUrl = $rel_path.'/views/';

if(file_exists('.env')){
  $env = extract(parse_ini_file('.env'));
}

if(isset($DBName) and ($DBName <> '')){
  $db['write']['DBHost'] = $DBHost;
  $db['write']['DBName'] = $DBName;
  $db['write']['DBUser'] = $DBUser;
  $db['write']['DBPass'] =$DBPass;

  $db['read']['DBHost'] = $DBHost;
  $db['read']['DBName'] = $DBName;
  $db['read']['DBUser'] = $DBUser;
  $db['read']['DBPass'] = $DBPass;
}

/*
foreach($_GET as $k=>$v){
${"get_$k"} = $v;
}
 */

if (!isset($debug)) {
  $debug=0;
}
if ($debug==1) {
  $GLOBALS['minify_code'] = false;
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(1);
} else {
  $GLOBALS['minify_code'] = true;
  ini_set('display_errors', 0);
  ini_set('display_startup_errors', 0);
  error_reporting(0);
}
if (!isset($minify_html)) {
  $GLOBALS['minify_code'] = true;
} else {
  $GLOBALS['minify_code'] = $minify_html;
}

if (!isset($debugIps)) {
  $debugIps= array("127.0.0.1");
}

/*
if ($DBHost<>"" && $DBName<>"" && $DBUser<>"") {
$my = new mysqli($DBHost, $DBUser, $DBPass, $DBName);
$my->set_charset("utf8mb4");
}
 */

/* if (session_id() == "") { */
session_start();
/* } */


if($_REQUEST){
  /* checkPostRepeat(); */
}
function checkPostRepeat(){
  // check against refreshes when POSTng
  if($_POST){
    $RequestSignature = md5($_SERVER['REQUEST_URI'].$_SERVER['QUERY_STRING'].print_r($_POST, true));
    if ($_SESSION['LastRequest'] == $RequestSignature){
      header("HTTP/1.1 303 See Other");
      header("Location: ".$_SERVER['HTTP_REFERER']);
      die();
    }else{
      $_SESSION['LastRequest'] = $RequestSignature;
      return true;
    }
  }
}


function sessionFlash($msg,$type = 'success'){
  $a = array(
    'type' => $type,
    'msg' => $msg
  );
  $_SESSION['msg'][] = $a;

}

function sessionFlashShow(){
  if(!empty($_SESSION['msg'])){
    foreach($_SESSION['msg'] as $m){
      if($m['msg']<> ''){
        switch($m['type']){
        case "success": echo "<div class='alert alert-success'>{$m['msg']}</div>"; break;
        case "error": echo "<div class='alert alert-danger'>{$m['msg']}</div>"; break;
        case "danger": echo "<div class='alert alert-danger'>{$m['msg']}</div>"; break;
        case "warning": echo "<div class='alert alert-warning'>{$m['msg']}</div>"; break;
        case "info": echo "<div class='alert alert-info'>{$m['msg']}</div>"; break;
        default: echo "<div class='alert alert-info'>{$m['msg']}</div>";
        }
      }
    }
  }
  unset ($_SESSION['msg']);
}


// load languages
if(strtolower(clean($_SESSION['lang']))<>''){
  $lng = strtolower(clean($_SESSION['lang']));
  $_SESSION['lang'] = $lng;
}else{
  $lng='en';
}
if(file_exists(__DIR__."/lang/{$lng}.php")){
  require_once(__DIR__."/lang/{$lng}.php");
}else{
  require_once(__DIR__."/lang/en.php");
}

function langChange(){
  global $enabledLanguages;
  $currentl = $_SESSION['lang'];
  $o=L('Change language')." <div class='btn-group'>
    <button type='button' class='btn btn-default'><img src='/lang/$currentl.png' /> ".strtoupper($currentl)."</button>
    <button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown' aria-expanded='false'>
    <span class='caret'></span>
    <span class='sr-only'>".L('Change language')."</span>
    </button>
    <ul class='dropdown-menu' role='menu'>";
  foreach($enabledLanguages as $l){
    $o.="<li><a href='/lang/$l'><img src='/lang/$l.png'/> ".strtoupper($l)."</a></li>";
  }
  $o.="</ul></div>";
  echo $o;
}


// Usage: mysql_magic($query [, $arg...]);
function mql($q) {
  global $db,$debug;
  if (startsWith($q, 'delete') || startsWith($q, 'update') || startsWith($q, 'insert') || startsWith($q, 'replace')){
    if (!$myWrite){
      $myWrite = new mysqli($db['write']['DBHost'], $db['write']['DBUser'], $db['write']['DBPass'], $db['write']['DBName']);
      $myWrite->set_charset("utf8mb4");
    }
    $my = $myWrite;
    /* print_r($my); */
    $req_result = $my->query($q);
    if (!$req_result){
      if ($debug==1) {
        printf("mysqlError: %s\nFOR Q: %s\n", $myWrite->error, $q);
      }
      return false;
    }
  }else{
    if (!$myRead){
      $myRead = new mysqli($db['read']['DBHost'], $db['read']['DBUser'], $db['read']['DBPass'], $db['read']['DBName']);
      $myRead->set_charset("utf8mb4");
    }
    $my = $myRead;
    $req_result = $my->query($q) or $my->error;
    if (!$req_result) {
      if ($debug==1) {
        printf("mysqlError: %s\nFOR Q: %s\n", $myWrite->error, $q);
      }
      return false;
    }
  }

  if (startsWith($q, 'delete') || startsWith($q, 'update') || startsWith($q, 'create') || startsWith($q, 'drop')){
    return $my->affected_rows; // -1 || N
  } else if (startsWith($q, 'insert')){
    return $my->insert_id; // ID || 0 || FALSE
  } else if (endsWith($q, 'limit 1')){
    return $req_result->fetch_assoc(); // [] || FALSE
  } else if (startsWith($q, 'select count(*)')){
    $line = $req_result->fetch_row();
    return $line[0]; // N
  } else {
    if($req_result){
      while($row = $req_result->fetch_assoc()){
        $r[]=$row;
      }
      return $r;
    }
  }
}

function startsWith($haystack,$needle) {
  return (strcasecmp(substr($haystack, 0, strlen($needle)),$needle)===0);
}

function endsWith($haystack,$needle) {
  return (strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);
}


function createEditPostData($allowedFields,$tableName, $rec_id = null){
  $a = array();
  if(!empty($_POST)){
    foreach($_POST as $k=>$p){
      $k = clean(preg_replace("/{$tableName}_([a-z]+).*/m",'\\1',$k),'\-\_');
      if(in_array("{$k}",$allowedFields)){
        if($k === 'password'){
          if($p<>''){
            $a[$k] = genPass($p);
          }
        }else{
          /* $a[$k] = clean($p,'\_\-\@\.\,\ \"\#\!\$\%\&\*'); */
          $a[$k] = clean(htmlspecialchars($p),'\_\-\@\.\,\ \"\#\!\$\%\&\*\;\/\<\>\{\}\:\[\]\(\)');
        }
      }
    }
  }

  if($rec_id <> ''){
    //editing
    $ao='';
    foreach ($a as $ak=>$av){
      if(is_numeric($av)){
        /* $aa[$ak] = "$av"; */
        $ao.="{$ak}={$av},";
      }else{
        /* $aa[$ak] = "\"$av\""; */
        $ao.="{$ak}=\"{$av}\",";
      }
    }
    $ao = substr($ao,0,-1);
    /*
    foreach($aa as $ak=>$aa){
      $ao.="{$ak}={$aa},";
    }
     */
    $q = "UPDATE $tableName set $ao where id=$rec_id";
  }else{
    if(!empty(array_values($a))){
      //adding
      foreach (array_values($a) as $av){
        if(is_numeric($av)){
          $aa[] = "$av";
        }else{
          $aa[] = "\"$av\"";
        }
      }
      $q = "INSERT INTO $tableName (".implode(',',array_keys($a)).") VALUES (".implode(',',array_values($aa)).")";
      echo $q;
    }
  }
  $qr = mql($q);
  if($qr){
    sessionFlash('Success');
    header('Location: /'.$tableName);
    die();
    /* }else{ */
    /*   sessionFlash('Error: User or Email might be taken','error'); */
  }

}

// autocreate FORM by getting the 'describe dbtable'
function createEditTableFromMysqlTable($tablename,$editTable=1,$rec_id='',$method='POST',$allowed_fields = array(),$readonly = array(),$more=array()){
  $q = mql("desc $tablename");
  $counter=1;
  if(empty($q)){
    return null;
  }

  foreach ($q as $q){
    //  d($q);
    if((empty($allowed_fields)) || ((!empty($allowed_fields)) AND (in_array($q['Field'],$allowed_fields)))){
      //if(($q['Extra']=='auto_increment') or ($q['Key']=='UNI') or ($q['Type']=='timestamp')){
      if(($q['Extra']=='auto_increment') or ($q['Type']=='timestamp')){
        continue; // skip INDEXES
      }

      $f[$counter]['name']= $tablename."_".$q['Field'];
      $f[$counter]['name_only']= $q['Field'];
      if(in_array($q['Field'],$readonly)){
        $f[$counter]['readonly']=true;
      }else{
        $f[$counter]['readonly']=false;
      }
      /* preg_match('/(\w+)\((\d+)\)/',$q['Type'],$match); */
      preg_match('/(\w+)\(?(\d+)?\)?/',$q['Type'],$match);
/*
      if($q['Null']=="NO"){
        $f[$counter]['required'] = 'required';
      }else{
        $f[$counter]['required'] = '';
      }
 */
      $f[$counter]['max'] = intval($match[2]);
      $f[$counter]['default'] = $q['Default'];

      if(($match[1]=='varchar') || ($match[1]=='float')){
        $f[$counter]['type'] = 'text';
      }elseif(in_array($match[1],array('int','tinyint','bigint'))){
        if($match[2]==1){
          $f[$counter]['type'] = 'radio';
        }else{
          $f[$counter]['type'] = 'text';
        }
      }elseif($match[1]=='text'){
        $f[$counter]['type'] = 'textarea';
      }


      // more / overrides
      foreach($more as $k=>$v){
        if($k==$q['Field']){
          foreach($more[$k] as $k=>$v){
            if($more[$q['Field']][$k]){ $f[$counter][$k] = $v; }
          }
        }
      }
      $counter++;
    }
  }

  // we have all relative fields that we want to work on in $f
  // now create the form
  if($editTable){
    $edit = ucfirst($tablename).'Edit';
  }else{
    $edit = ucfirst($tablename).'Add';
  }
  $o = "<form class='panel-body form-horizontal form-padding col-4' method='$method' id='form$edit'>";
  foreach($f as $f){
    //get info from DB for this id in this table
    $dbvalue = mql("select * from $tablename where id=$rec_id limit 1");
    //d($dbvalue);
    //    d($f);
    if($editTable){
      $Fieldname = $f['name']."_".intval($rec_id);
    }else{
      $Fieldname = $f['name'];
    }

    if($f['min']){ $min="min='{$f['min']}'"; }else {$min='';}
    if($f['max']){ $max="max='{$f['max']}'"; }else {$max="max='{$f['max']}'";}
    if($f['minlength']){ $minlength="minlength='{$f['minlength']}'"; }else {$minlength="minlength='{$f['minlength']}'";}
    if($f['maxlength']){ $maxlength="maxlength='{$f['maxlength']}'"; }else {$maxlength="maxlength='{$f['maxlength']}'";}
    if($f['placeholder']){ $placeholder="placeholder='{$f['placeholder']}'"; }else {$placeholder="placeholder='{$f['name_only']}'";}
    if($f['label']){ $label=$f['label']; }else {$label=ucfirst($f['name_only']);}
    if($f['class']){ $class=$f['class']; }else {$class='';}
    if($f['required']){ $required="required='{$f['required']}'"; }else {$required='';}
    if($f['other']){ $other=$f['other']; }else {$other='';}

    if($f['type']=='password'){ $value=''; }else {$value=$dbvalue[$f['name_only']];}

    if($f['field_suffix']){
      $fs_part1="<div class='input-group'>";
      $fs_part2="<div class='input-group-addon'>{$f['field_suffix']}</div></div>";
    }else{
      $fs_part1='';
      $fs_part2='';
    }

    if($f['readonly']){
      $o.="
<div class='form-floating mb-2'>
<input type='text' id='$Fieldname' class='form-control $class' disabled=disabled readonly=readonly value='$value' $style>
<label class='control-label' for='$Fieldname'>$label</label>
</div>
";
    }elseif(in_array($f['type'],array('text','email','number','password','search','tel','time'.'url','date'))){
      $o.="
<div class='form-floating mb-2'>
<input type='{$f['type']}' id='$Fieldname' class='form-control form-control-sm $class' name='$Fieldname' value='$value' $style $placeholder $min $max $maxlength $minlength $required autocomplete='off' $other>
<label class='control-label' for='$Fieldname'>$label</label>
$fs_part2
</div>
";
    }elseif($f['type']=='radio'){
      $o.="
<div class='form-group'>
<label class='col-md-3 control-label' for='$Fieldname'>$label</label>
<div class='col-md-4 mb-2'>
<select class='selectpicker form-control $class' name='$Fieldname' id='$Fieldname' $style>
<option value='1'"; if(intval($value)==1){$o.= ' selected ';} $o.= " >".L('Yes')."</option>
<option value='0'"; if(intval($value)==0){$o.= ' selected ';} $o.= " >".L('No')."</option>
</select>
</div>
</div>
";

    }elseif($f['type']=='textarea'){
      $o.="
<div class='form-group'>
<label class='col-md-3 control-label' for='$Fieldname'>$label</label>
<div class='col-md-3'>
<textarea id='$Fieldname' name='$Fieldname' class='form-control $class' $style $placeholder >$value</textarea>
</div>
</div>
";
    }
  }


  if($editTable){
    $deletebutton = "<button type='submit' disabled class='btn btn-danger'><span class='material-icons'> delete </span></button>";
  }else{
    $deletebutton = '';
  }
  $o.="
              <div class='box-footer col-md-3 col-md-offset-3'>$deletebutton
                <button type='submit' class='btn btn-success pull-right' title='Go'>
<span class='material-icons'>
check
</span>
</button>
              </div>

</form>";

  return $o;
}


function ajaxDbUpdate($otherChecks='',$excluded = array('id','username','admin','created_at')){
  global $userfilespath;
  header("Content-Type: text/plain");
  //  header('Content-Type: application/json');
  // $request = dbtablename_key_recid = value;
  $check = explode('_',$_POST['data']);
  if(is_numeric($check[2])){
    if($check[2]<>$_SESSION['uid']){
      if($_SESSION['admin']<'1'){
        header('HTTP/1.0 403 Forbidden');
        die();
      }
    }
  }
  $o = array();
  switch($_SERVER["REQUEST_METHOD"]){
/*
//disabled due to security..
case 'PUT':
parse_str(file_get_contents("php://input"),$request);
foreach($request as $k=>$v){
if(strpos($k,'_') and !strpos($k,'!')){
$f = explode('_',$k);
$dbtable = $f[0];
$field[] = "'${f[1]}'";
if(is_numeric($v)){
$newvalue[] = $v;
}else{
$newvalue[] = "'$v'";;
}
}
}
$fld = implode(',',$field);
$val = implode(',',$newvalue);
if(isset($dbtable) AND isset($fld) AND isset($val)){

$q = "INSERT INTO $dbtable $fld VALUES($val)";
echo $q;
//    $x= mql($q);
}
break;
 */
  case 'POST':
    foreach($_POST['data'] as $k=>$v){
      //        if((strpos($k,'_')!== false) and (strpos($k,'!') === false)){
      $f = explode('_',$k);
      $dbtable = $f[0];
      $field = $f[1];
      $modifiedId = $f[2];
      if($newvalue=='on'){
        $newvalue=1;
      }elseif($newvalue=='off'){
        $newvalue=0;
      }
      if(is_numeric($v)){
        $newvalue = $v;
      }else{
        $newvalue = "'$v'";;
      }
    }
    if(!in_array($field,$excluded)){
      $update.="$field=$newvalue,";
    }
    //      }
    if(substr($update,-1)==','){
      $update = substr($update,0,-1);
    }
    if(isset($dbtable) AND is_numeric($modifiedId) AND isset($update)){
      // get old fields for log
      if($dbtable=='users'){
        $q = "SELECT * from $dbtable where id='".intval($modifiedId)."' limit 1";
      }else{
        $q = "SELECT * from $dbtable where id='".intval($modifiedId)."' limit 1";
      }
      $old = mql($q);
      if($dbtable=='users'){
        $log_name = $old['name'];
        $log_user = $old['username'];
        $log_uid = $old['id'];
      }else{
        $log_u = getAllUsers($old['uid']);
        $log_name = $log_u['name'];
        $log_user = $log_u['username'];
        $log_uid = $log_u['id'];
      }
      //  logger($log_uid,$_SESSION['uid'],"stepChange",$_SESSION['name']." смени: $field от: {$old[$field]} на: $newvalue");

      if($field == 'delete'){
        $q = "DELETE FROM $dbtable where id='$modifiedId' $otherChecks";
      }else{
        $q = "UPDATE $dbtable set $update where id='$modifiedId' $otherChecks";
      }
      //echo $q;
      mql($q);

      $x['success']=true;
    }
    break;

  case 'DELETE':
    parse_str(file_get_contents("php://input"),$request);
    foreach($request as $k=>$v){
      if(strpos($k,'_') and !strpos($k,'!')){
        $f = explode('_',$k);
        $dbtable = $f[0];
        $field = "'${f[1]}'";
        if(is_numeric($f[2])){
          $modifiedId = $f[2];
        }elseif(is_numeric($f[1])){
          $modifiedId = $f[1];
        }else{
          return false;
        }
        if(is_numeric($v)){
          $newvalue = $v;
        }else{
          $newvalue = "'$v'";;
        }
      }
    }
    //do not use the $update, just delete the ID
    if(isset($dbtable) AND is_numeric($modifiedId)){
      $q = "DELETE from $dbtable where id='$modifiedId'";
      echo $q;
      //    $x= mql($q);
    }
    break;

  default: // GET
    //   $x = mql("SELECT * from $dbtable where $field='$newvalue' $otherChecks LIMIT 1");
    //print_r(Flight::request()->query['id']);
    die();
    break;
  }
  $o = json_encode($x, JSON_UNESCAPED_UNICODE);
  return $o;
  die();
}


function url(){
  global $webroot; //needs to be defined in the index
  $k=$_SERVER['REQUEST_URI'];
  if(substr($k,0,strlen($webroot)) == $webroot){
    $k=str_replace($webroot,"",$k);
  }
  $i=substr($k,1,strlen($k));
  $i=urldecode($i);
  $i=explode("/",$i);
  return $i;
}

function debug($i,$name=''){
  global $debug, $debugIps;
  if($name==""){
    foreach($GLOBALS as $k => $v) {
      if ($v === $i) {
        $name = $k;
      }
    }
  }
  if(in_array($_SERVER['REMOTE_ADDR'],$debugIps)){
    if(($debug) OR ($_GET['debug']==1)){
      //    echo "<pre>Debug: {$name}<br/>";
      echo "<pre>Debug: {$name} ".__FUNCTION__." ".__CLASS__."<br/>";

      if(!empty($i)){ print_r($i);}
      echo "</pre>";
    }
  }
}
function d($str,$name=""){
  if($str<>""){
    return debug($str,$name);
  }else{
    return debug($_SESSION,$name);
  }
}
function htmldecode($k) {
  $i=urldecode($k);
  return $i;
}

function mini($buffer) {
  global $minify_html;
  if($minify_html){
    $search = array(
      '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
      '/[^\S ]+\</s',     // strip whitespaces before tags, except space
      '/(\s)+/s',         // shorten multiple whitespace sequences
      '/<!--(.|\s)*?-->/', // Remove HTML comments
      /*  '/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s' */
    );

    $replace = array(
      '>',
      '<',
      '\\1',
      '',
      /* ' ' */
    );

    $buffer = preg_replace($search, $replace, $buffer);
  }
  return $buffer;

}

function downloadFile($uid,$file){
  if (!isset($_SESSION['uid']))
  {
    exit;
    die();
  }
  if(file_exists($file)){
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    flush();
    readfile($file);
  }
  exit;
  die();
}

function is_cli($checkCurl=1) {
  if (defined('STDIN')) {
    return true;
  }
  if (php_sapi_name() === 'cli') {
    return true;
  }
  if (array_key_exists('SHELL', $_ENV)) {
    return true;
  }
  if (empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0) {
    return true;
  }
  if (!array_key_exists('REQUEST_METHOD', $_SERVER)) {
    return true;
  }
  if ($checkCurl==1) {
    if (preg_match('/curl.*/', $_SERVER['HTTP_USER_AGENT'], $matches)) {
      return true;
    }
  }
  return false;
}

function clean($i, $allowed='') {
  $i = trim($i);
  $i = strip_tags($i);
  $i = preg_replace('/[^A-Za-z0-9А-Яа-я'.$allowed.']/u', '', $i);
  return $i;
}

function sanitize_num($input) {
  $input = preg_replace("/[^0-9]/", "", $input);
  if ($input == '') {
    $input = 0;
  }
  return $input;
}

function sanitizeData($q) {
  foreach ($q as $n => $v) {
    $r[$n] = clean($v, "\.\,\!\?\=\@\-\_\#\№\ А-Яа-я");
  }
  return $r;
}


function generateRandomString($chars='',$length = 10) {
  $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ$chars";
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
    $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  return $randomString;
}
function doubleSalt($toHash) {
  global $passwordSalt;
  $password = str_split($toHash, (strlen($toHash)/2)+1);
  $hash = hash('sha256', $password[0].$passwordSalt.$password[1]);
  return $hash;
}

function genPass($pass){
  return password_hash($pass, PASSWORD_DEFAULT, ['cost'=>12]);
}

function hashPass($hash, $myPass='') {
  if ($myPass=='') {
    return false;
  } else {
    if (password_verify($myPass, $hash)) {
      return true;
    } else {
      return false;
    }
  }
}

function array_clean_empty($haystack, $remove="") {
  foreach ($haystack as $key => $value) {
    if (is_array($value)) {
      $haystack[$key] = array_clean_empty($haystack[$key]);
    }

    if (empty($haystack[$key])) {
      unset($haystack[$key]);
    }
  }
  return $haystack;
}

function jsonit($rows, $json=1) {
  $r = array();
  $r['now']=date("c");
  $r['nowu']=date("U");
  $r['ip'] = $_SERVER["REMOTE_ADDR"];
  $r['agent'] = $_SERVER["HTTP_USER_AGENT"];
  $r["data"]=$rows;
  if ($json==1) {
    return json_encode($r, JSON_UNESCAPED_UNICODE);
  } elseif ($json==2) {
    return json_encode($r, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
  } else {
    return json_encode($r);
  }
}

function isJson($string) {
  json_decode($string);
  return (json_last_error() == JSON_ERROR_NONE);
}

function toJson($r,$pretty=false){
  if(($pretty) or ($_REQUEST['pretty']=='1')){
    return json_encode($r, JSON_UNESCAPED_UNICODE|JSON_HEX_QUOT|JSON_PRETTY_PRINT|JSON_PARTIAL_OUTPUT_ON_ERROR|JSON_UNESCAPED_SLASHES);
  }else{
    return json_encode($r, JSON_UNESCAPED_UNICODE|JSON_HEX_QUOT|JSON_PARTIAL_OUTPUT_ON_ERROR|JSON_UNESCAPED_SLASHES);
  }
}

function ifShow($one, $more='') {
  if ($one<>'') {
    return $one;
  } else {
    if ($more<>'') {
      return $more;
    }
  }
}

function ifShowDate($one, $more='') {
  if ($one<>'') {
    return '<h2>'.$one.'</h2><div class="panel panel-colorful panel-success"><div class="panel-heading"><h3 class="panel-title">Процесът е успешно приключен!</h3></div></div>';
  } else {
    if ($more<>'') {
      return $more;
    }
  }
}

function getIp() {
  $headers = $_SERVER;
  //Get the forwarded IP if it exists
  if (array_key_exists('X-Forwarded-For', $headers) && filter_var($headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
    $the_ip = $headers['X-Forwarded-For'];
  } elseif (array_key_exists('HTTP_X_FORWARDED_FOR', $headers) && filter_var($headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
  ) {
    $the_ip = $headers['HTTP_X_FORWARDED_FOR'];
  } else {
    $the_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
  }
  return $the_ip;
}
function isIpv4($ip_addr) {
  //first of all the format of the ip address is matched
  if (preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/", $ip_addr)) {
    //now all the intger values are separated
    $parts=explode(".", $ip_addr);
    //now we need to check each part can range from 0-255
    foreach ($parts as $ip_parts) {
      if (intval($ip_parts)>255 || intval($ip_parts)<0) {
        return false;
      } //if number is not within range of 0-255
    }
    return true;
  } else {
    return false;
  } //if format of ip address doesn't matches
}

function domain($domainb) {
  $bits = explode('/', $domainb);
  if ($bits[0]=='http:' || $bits[0]=='https:') {
    $domainb= $bits[2];
  } else {
    $domainb= $bits[0];
  }
  unset($bits);
  $bits = explode('.', $domainb);
  $idz=count($bits);
  $idz-=3;
  if (strlen($bits[($idz+2)])==2) {
    $url=$bits[$idz].'.'.$bits[($idz+1)].'.'.$bits[($idz+2)];
  } elseif (strlen($bits[($idz+2)])==0) {
    $url=$bits[($idz)].'.'.$bits[($idz+1)];
  } else {
    $url=$bits[($idz+1)].'.'.$bits[($idz+2)];
  }
  return $url;
}

function theDate($date) {
  if ((preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $date, $d)) or (preg_match("/^(\d{4})(\d{2})(\d{2})$/", $date, $d))) {
    $r['input'] = $d[0];
    $r['y'] = $d[1];
    $r['m'] = $d[2];
    $r['d'] = $d[3];
    $r['dow'] = strtolower(date("l", mktime(0, 0, 0, $d[2], $d[3], $d[1])));
    $r['doe'] = date("z", mktime(0, 0, 0, $d[2], $d[3], $d[1]))+1;
    $r['isLeap'] = date("L", mktime(0, 0, 0, $d[2], $d[3], $d[1]));
    $r['isoDate'] = date("c", mktime(0, 0, 0, $d[2], $d[3], $d[1]));
    $r['rfcDate'] = date("r", mktime(0, 0, 0, $d[2], $d[3], $d[1]));
    $r['week'] = date("W", mktime(0, 0, 0, $d[2], $d[3], $d[1]));

    //print_r($r);
  }
  return $r;
}


function seo($q) {
  // make the " " = "_"
  return str_replace(" ", "_", $q);
}
function deseo($q) {
  // make the "_" = " "
  return str_replace("_", " ", $q);
}


function createNewUser($request){
  $field[]='id';
  $newvalue[] = "''";
  foreach($request as $k=>$v){
    if(strpos($k,'_') and !strpos($k,'!')){
      $f = explode('_',$k);
      $dbtable = $f[0];
      $field[] = "${f[1]}";
      if(is_numeric($v)){
        $newvalue[] = $v;
      }else{
        $newvalue[] = "'$v'";;
      }
    }
  }
  $fld = implode(',',$field);
  $val = implode(',',$newvalue);
  if(isset($dbtable) AND isset($fld) AND isset($val)){

    $q = "INSERT INTO $dbtable ($fld) VALUES ($val)";
    //echo $q;
    $x = mql($q);
    if(is_numeric($x)){
      return $x;
    }else{
      //hm user was not created via the insert..
      return true;
    }
  }
}

function createTableFromArray($a, $ignoredHeaders = array(), $renamedHeaders = array(), $mappedValues = array()){
  if(!$a){ return false;}
  $aHeaders = array_keys($a[0]);
  if(!$aHeaders){ return null;}
  foreach($aHeaders as $ahk=>$ah){
    if(!in_array($ah,$ignoredHeaders)){
      $aHeaders2[$ahk] = $ah;
    }
  }
  if(!$aHeaders2){ return null;}
  $aHkeys = array_keys($aHeaders2);
  $aHVals = array_values($aHeaders2);
  $ret = "<table class='table table-bordered table-sm table-hover'><tr>";
  foreach($aHeaders2 as $ah2k=>$ah2){
    if(in_array($ah2k,$aHkeys)){
      if($renamedHeaders[$ah2]){
        $ah2 = $renamedHeaders[$ah2];
      }
      if($ah2){
        $ah2 = ucfirst($ah2);
        $ah2 = str_replace('_',' ',$ah2);
        $ret.= "<th>$ah2</th>";
      }else{
        $ret.= "<th>&nbsp;</th>";
      }
    }
  }
  $ret .="</tr>\n";

  foreach($a as $aki=>$avi){
    $ret .= "<tr class='tr_{$avi['id']}'>";
    foreach($avi as $ak=>$av){
      if(in_array($ak,$aHVals)){
        if($av){
          if($mappedValues[$ak]){
            $av = array_map($mappedValues[$ak],array($av))[0];
          }
          $ret.= "<td class='td_{$ak}-{$avi['id']}'>{$av}</td>";
        }else{
          $ret.= "<td>&nbsp;</td>";
        }
      }
    }
    $ret .="</tr>\n";
  }


  $ret .= "</table>";
  return $ret;
}


// get global Server Variables + others in this array, so they can be insterted easily..
function s() {
  global $webroot;
  if ($_SERVER["SERVER_PORT"]<>"80") {
    $http = "https";
  } else {
    $http = "http";
  }
  $s = array(
    'server_name' => $_SERVER["SERVER_NAME"],
    'server_port' => $_SERVER["SERVER_PORT"],
    'server_http' => $http,
    'server_ip' => $_SERVER["SERVER_ADDR"],
    'server_time' => $_SERVER["REQUEST_TIME"],
    'server_rip' => $_SERVER["REMOTE_ADDR"],
    'ip' => getIp(),
    'server_protocol' => $_SERVER["SERVER_PROTOCOL"],
    'server_agent' => $_SERVER["HTTP_USER_AGENT"],
    'uri' => $_SERVER["REQUEST_URI"],
    'httpmethod' => $_SERVER["REQUEST_METHOD"],
    'scriptname' => $_SERVER["SCRIPT_NAME"],
    'webuser' => $_SERVER["USER"],
    'webhome' => $_SERVER["HOME"],
    'docroot' => $_SERVER["DOCUMENT_ROOT"],
    'approot' => $http."://".$_SERVER["SERVER_NAME"].$webroot,
    //'token' => bin2hex(openssl_random_pseudo_bytes(32)),
    'cli' => is_cli(),
    'url' => url(),
  );
  return $s;
}
$s = s(); // get server global variables
