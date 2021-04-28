<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv='content-type' content='text/html; charset=utf-8' />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="/favicon.png" />
    <title><?php echo $sitename; ?></title>

    <meta name="description" content="<?php echo $app['site_description']; ?>" />
    <meta name="Keywords" content="<?php if (count($app['site_keywords'])<1) {
    echo deseo($app['site_name']);
    } else {
    echo $app['site_keywords'];
    } ?>">

    <meta property='og:type' content='website' />
    <meta property='og:url' content='<?php echo $s['approot']; ?>' />
    <meta property='og:title' content='<?php echo $title; ?>' />
    <meta property='og:description' content='<?php echo $app['site_description']; ?>' />


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link href="<?php echo $webroot; ?>/jquery.dataTables.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>

    <!-- <script src="https://unpkg.com/ionicons@5.1.2/dist/ionicons.js"></script> -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <script type="text/javascript" src="<?php echo $webroot; ?>/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo $webroot; ?>/jquery.dataTables.min.js"></script>

    <style>
li.nav-item > a.nav-link{ text-transform:capitalize; }
.material-icons{ vertical-align:middle; line-height:0; }
body > .navbar-default{ position: fixed; width: 100vw; top: 0; z-index:10000; }
    </style>

    <style>
textarea{ padding:6px 8px;}
.nicEdit-main {padding:2px 4px;}
.mb-2{margin-bottom:10px;padding:0;}
.material-icons.md-18 { font-size: 18px; }
.material-icons.md-24 { font-size: 24px; }
.material-icons.md-36 { font-size: 36px; }
.material-icons.md-48 { font-size: 48px; }

.table td, .table th{ font-size:0.9rem; }
table.dataTable, table.dataTable th, table.dataTable td{
font-size:0.9rem;
}
    </style>

  </head>
  <body>


    <nav class="navbar navbar-expand-lg sticky-top navbar-dark bg-primary">
      <div class="container-fluid">
        <a class="navbar-brand" href="/"><?php echo $sitename; ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item"> <a class="nav-link <?php if($u[0]===''){ echo 'active';}?>" aria-current="page" href="/">Dashboard</a> </li>
          </ul>


          <ul class="navbar-nav d-flex ml-2 order-3 pr-3">
            <li class="nav-item"> <a class="nav-link <?php if($u[0]==='users'){ echo 'active';}?>" aria-current="page" href="/users">Users</a> </li>
            <li class="nav-item"><a class="nav-link" href="/profile">Profile</a></li>
            <li class="nav-item"><a class="nav-link" href="/logout" title='Logout' data-bs-toggle="tooltip" data-bs-placement="left"><i class="material-icons md-18">exit_to_app</i></a></li>
          </ul>

        </div>
      </div>
    </nav>


    <div class="container-fluid">
      <br>
      <br>
      <?php
        sessionFlashShow();
        ?>
