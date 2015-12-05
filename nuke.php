<!-- Drops the database - for debugging only.
     We obviously would not have this in a production system! -->
<?php
include 'lib/core.php';
Nuke();
echo('database dropped!');
?>
