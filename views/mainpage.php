<div class="card mb-2 tags">
  <div class="card-body">

<?php
$thr = array();
$th_to_ignore = array('id','full');
$th = mql('SHOW COLUMNS FROM servers');
foreach($th as $th){
  if(!in_array($th['Field'],$th_to_ignore)){
    $thr[] = $th['Field'];
  }
}
/* print_r($thr); */
foreach($thr as $t){
  $tname = str_replace('_',' ',$t);
  echo "<span id='tag-{$t}' class='badge bg-secondary'>{$tname}</span> ";
}
?>

  </div>
</div>

<script>
var readhash = location.hash.split('|');
console.log('rh',readhash);
if(readhash.length > 0){
  readhash = removeA(readhash, '#');
  $.each(readhash, function( i, v ) {
    $('#tag-' + v).addClass('bg-primary').removeClass('bg-secondary');
});
}else{
  /* maybe set some default colums here */
}
$(document).on('click', '[id^="tag-"]',function(e){
  let id = $(this).attr('id').split('-')[1];
  $(this).toggleClass('bg-secondary bg-primary');
  // update the hash
  var x = location.hash;
  var xx = x.split('|');

  if($(this).hasClass('bg-primary')){
    /* add primary */
    xx.push(id);
}else{
  /* removal */
  xx = removeA(xx, id);
}
xclean = [...new Set(xx)];
console.log('xclean',xclean);
location.hash = xclean.join('|');
updateTable();

//once hash is updated, save hash into local.storage


});

function updateTable(){
  var encData = encodeURIComponent(location.hash);
  /* $.get("/api/?table=main&tags=" + encData); */

  $.post('/api/', {table:'main', tags: encData }, function(r){
    console.log(r);

    /* var datatable = $('#maintable').DataTable(); */
    /*     datatable.clear(); */
    /*     datatable.rows.add(r.data); */
    /*     datatable.draw(); */

    location.reload();

});
}

function removeA(arr) {
  var what, a = arguments, L = a.length, ax;
  while (L > 1 && arr.length) {
    what = a[--L];
    while ((ax= arr.indexOf(what)) !== -1) {
      arr.splice(ax, 1);
        }
    }
    return arr;
}



$(document).ready(function() {
  $('#maintable').DataTable( {
  "ajax": "/api/",
    "deferRender": true,
    "pageLength": 200,
    "stateSave": true,
    "columns": [ <?php
foreach($_SESSION['tagsArr'] as $k=>$tag){
  if (end(array_keys($_SESSION['tagsArr'])) == $k){
    echo "{ \"data\": \"{$tag}\" }";
  }else{
    echo "{ \"data\": \"{$tag}\" },";
  }
}
?> ]
    } );
    } );



















</script>



<table id="maintable" class="display nowrap" style="width:100%">
        <thead>
            <tr>
    <?php foreach($_SESSION['tagsArr'] as $k=>$tag){ $tag = ucfirst(str_replace('_',' ',$tag)); echo "<th>{$tag}</th>"; } ?>
            </tr>
        </thead>
        <tfoot>
            <tr>
    <?php foreach($_SESSION['tagsArr'] as $k=>$tag){ $tag = ucfirst(str_replace('_',' ',$tag)); echo "<th>{$tag}</th>"; } ?>
            </tr>
        </tfoot>
    </table>


<?php

//allServersTabe();
