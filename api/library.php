<?php

require_once("../lib/database.php");

$db = new Database();

$sql = "select id as ID, name as Name, parts as Parts, difficulty as Difficulty, note_duration_ms as 'Beat Length' from pieces order by id desc";
$result = $db->query($sql);

echo json_encode($result);

?>