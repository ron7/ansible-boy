<?php if(empty($_SESSION['username'])){header('Location: //'.$_SERVER["SERVER_NAME"].'/');exit;}
$u0 = clean($u[0]);

if($_POST['action'] == 'delete' && $_POST['id']<>''){
  $delid = intval($_POST['id']);
  $q = "delete from $u0 where id=$delid";
  $del = mql($q);
  die();
}
// if we are POST adding a user:
if(in_array($u[1],array('add','edit'))){
  if($u[1]==='add'){
    createEditPostData(
      array('name','email','admin','status','username','password'),
      $u0);
  }elseif($u[1]==='edit'){
    createEditPostData(
      array('name','email','admin','status','password'),
      $u0,intval($u[2]));
  }
?>
    <section class="content-header">
      <h3>
      <a class='btn btn-sm btn-secondary' href='<?php echo $s['approot'].'/'.$u0.'/'; ?>'><span class="material-icons">arrow_back</span></a> <?php
      if($u[1]==='add'){
        echo L(ucfirst($u0)).' '.L('add');
      }elseif($u[1]==='edit'){
        echo L(ucfirst($u0)).' '.L('edit');
      }

?>
      </h3>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <div class="box">
            <div class="box-header">
            </div>
            <div class="box-body">
<?php
  //   createEditTableFromMysqlTable(tableName,edit?,rec_id_from_table,method,allowed_array,array_read_only,override_attributes_for_allowed_name)
  if($u[1]==='add'){
    echo createEditTableFromMysqlTable($u0,0,'','POST',
      array('name','email','admin','status','username','password'),
      array(),
      array('email'=>array('type'=>'email'),
      'password'=>array('type'=>'password','minlength'=>8),
      )
    );
  }elseif($u[1]==='edit'){
    echo createEditTableFromMysqlTable($u0,1,intval($u[2]),'POST',
      array('name','email','admin','status','username','password'),
      array('username'),
      array('email'=>array('type'=>'email'),
      /* 'username'=>array('field_suffix'=> "@{$_SESSION['csuffix']}"), */
      'password'=>array('type'=>'password','minlength'=>8),
      )
    );
  }
?>
            </div>
          </div>
        </div>
      </div>
</section>

<?php
}else{ // if not action but the main page
?>


<button class="btn btn-danger mb-3" id="resetDbTableServers">Reset DB Table Servers (this will drop al lthe data)</button>

                <section class="content-header">
                        <h3>
                                <?php echo L(ucfirst($u0)); ?>
 <a href='<?php echo $s['approot'].'/'.$u0.'/add/'; ?>' class="btn btn-primary btn-sm">
<span class="material-icons md-18">
person_add
</span>
</a>
                        </h3>
                </section>
                <section class="content">
                        <div class="row">
                                <div class="col">
                                        <div class="box">
                                                <div class="box-header">
                                                </div>
                                                <div class="box-body">
                                                        <table id="<?php echo $u0; ?>" class="table table-bordered table-sm">
                                                                <thead>
                                                                <tr>
                                                                        <th><?php echo L('Name'); ?></th>
                                                                        <th>E-mail</th>
                                                                        <th><?php echo L('Admin'); ?>?</th>
                                                                        <th><?php echo L('Status'); ?></th>
                                                                        <th><?php echo L('Other actions'); ?></th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>

<?php
  $loop = mql("select * from $u0");
  if(!empty($loop)){
    foreach($loop as $r){
      echo " <tr class='line_{$u0}_{$r['id']} {$r['state_class']}'>
        <td>{$r['name']}</td>
        <td>{$r['email']}</td>
        <td>{$r['is-admin']}</td>
        <td>{$r['state']}</td>
        <td><div class='btn-group btn-group-sm btn-sm'>
        <a href='$webroot/{$u0}/edit/{$r['id']}' id='{$u0}_edit_{$r['id']}' class='btn btn-success btn-sm' title='Edit'>
        <span class='material-icons md-18'>
edit
</span>
  </a>
  <button type='button' id='{$u0}_delete_{$r['id']}' class='btn btn-danger btn-sm' title='Delete'>
  <span class='material-icons md-18'>
delete
</span>
  </button>
  </div></td></tr>";
    }
  }
?>
                                                                </tbody>
                                                                <tfoot>
                                                                <tr>
                                                                        <th><?php echo L('Name'); ?></th>
                                                                        <th>E-mail</th>
                                                                        <th><?php echo L('Admin'); ?>?</th>
                                                                        <th><?php echo L('Status'); ?></th>
                                                                        <th><?php echo L('Other actions'); ?></th>
                                                                </tr>
                                                                </tfoot>
                                                        </table>
                                                </div>
                                        </div>
                                </div>
                        </div>
</section>

  <script>
  $( "[id^='<?php echo $u[0]; ?>_delete_']" ).on('click',function(e){
    let id = $(this).attr('id').split('_')[2];
    if(id !== 'undefined'){
      $.post("<?php echo $_SERVER['REQUEST_URI']; ?>", { action: "delete",id: id }).done(function(data){$('.line_<?php echo $u[0]; ?>_' + id).hide('fast', function(){ $('.line_<?php echo $u[0]; ?>_' + id).remove(); });});
}
});

$('#resetDbTableServers').on('click',function(){
  $.post("/api/", { action: "resetDbTableServers" });
});
</script>
<?php
}
?>
