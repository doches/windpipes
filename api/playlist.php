<?php

require_once("../lib/database.php");

$db = new Database();

$sql = "select playlist.id as playlist_id, name as Name, parts as Parts, difficulty as Difficulty, time(start_time) as 'Start Time' from playlist left join pieces on playlist.piece_id = pieces.id order by playlist.start_time";
$result = $db->query($sql);

echo json_encode($result);

?>