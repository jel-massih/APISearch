<?php
$db_link = new MongoClient("DBURL");

$db_link = $db_link->scavenger_hunt;

$apiCollection = $db_link->APITest;
?>