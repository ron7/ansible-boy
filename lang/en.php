<?php

// en
function L($text){
  static $l = array(
    'Board' => 'Board', // menu
    'Users' => 'Users',
    'Menu' => 'Menu',
    'Tables' => 'Tables',
    'Logout' => 'Logout',
    'Documentation' => 'Documentation',
    'Main menu' => 'Main menu',
    'System menu' => 'System menu',
    'Name' => 'Name', // users
    'Admin' => 'Admin',
    'Status' => 'Status',
    'Other actions' => 'Actions',
    'User edit' => 'User edit',
    'Back' => 'Back',
    'Delete' => 'Delete',
    'Add user' => 'Add user',
    'Yes' => 'Yes',
    'No' => 'No',
    'Existing' => 'Existing', //tables
    'Create new' => 'Create new',
    'Settings' => 'Settings', // settings
    'Close' => 'Close',
    'Change language' => 'Change language',



  );
  //return (!array_key_exists($text,$l)) ? "<div class='bg-danger' style='display:inline;text-decoration: underline;'>".$text."</div>" : $l[$text];
  return (!array_key_exists($text,$l)) ? $text : $l[$text];
}
