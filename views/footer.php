</div>

<div class="xron">
</div>

<script>
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})
</script>

</body>
</html>

<?php echo "<!-- Request time: ". number_format(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],4)." sec -->"; ?>
