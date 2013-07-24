<?php
require_once("../lib/database.php");

$db = new Database();

$id = intval($_REQUEST['id']);

$sql = "delete from playlist where id = '$id'";
$db->query($sql);

?>