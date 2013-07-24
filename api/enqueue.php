<?php
require_once("../lib/database.php");

$db = new Database();

$pieceID = intval($_REQUEST['id']);
$start_time = intval($_REQUEST['start']);
echo $start_time;

$sql = "insert into playlist (piece_id, start_time) values ('{$pieceID}', from_unixtime({$start_time}))";
$db->query($sql);

?>