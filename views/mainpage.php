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
sort($thr);
foreach($thr as $t){
  $tname = str_replace('_',' ',$t);
  echo "<span id='tag-{$t}' class='badge bg-secondary'>{$tname}</span> ";
}
if(!isset($_SESSION['tagsArr'])){
  $_SESSION['tagsArr'] = $defaulthashes;
}
?>

<a href='/' class='btn btn-sm badge bg-danger'>Clear all</a>
<button class='btn btn-sm badge bg-success' id="dldCsv">Download csv</button>
  </div>
</div>

<script>
/* console.log('he'); */
$(document).ready(function() {
  var readhash = location.hash.split('|');
  /* console.log('rh',readhash); */
  if(readhash.length > 1){
    readhash = removeA(readhash, '#');
    $.each(readhash, function( i, v ) {
      $('#tag-' + v).addClass('bg-primary').removeClass('bg-secondary');
});
}else{
  /* maybe set some default colums here */
<?php
echo "var defaulthashes = ['".implode("','",$defaulthashes)."'];";
//$_SESSION['tagsArr'] = $defaulthashes;
?>
$.each(defaulthashes, function( i, v ) {
  $('#tag-' + v).addClass('bg-primary').removeClass('bg-secondary');
  });
location.hash = '#|' + defaulthashes.join('|');
}
});
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
/* console.log('xclean',xclean); */
location.hash = xclean.join('|');
updateTable();


});

function updateTable(){
  var encData = encodeURIComponent(location.hash);

  $.post('/api/', {table:'main', tags: encData }, function(r){
    /* console.log(r); */

    /* var datatable = $('#maintable').DataTable(); */
    /*     /1* datatable.clear(); *1/ */
    /*     /1* datatable.rows.add(r.data); *1/ */
    /*     /1* datatable.draw(); *1/ */

    /*     datatable.destroy(); */
    /*     $('#maintable').empty(); // empty in case the columns change */

    /*     datatable = $('#maintable').DataTable( { */
    /*         columns: r.columns, */
    /*         data:    r.data */
    /*     } ); */


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

$('#maintable').DataTable().on( 'draw', function (e,v) {
  if((v.json.count !== 'undefine') && (parseInt(v.json.count) > 0)){
    let count = parseInt(v.json.count);
    $('.servers_count').text(count);
}
} );

    } );

$(document).on('click','#dldCsv',function(){
  var readhash = location.hash.split('|');
  if(readhash.length > 1){
    readhash = removeA(readhash, '#');
}
window.location = '/api/&download=csv&data=' + readhash.join('|');
})
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

