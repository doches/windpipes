<?php
require_once("../lib/database.php");

$db = new Database();

$ip = $_SERVER['REMOTE_ADDR'];

$sql = "select *, unix_timestamp(start_time) as timestamp, unix_timestamp(now()) as now, playlist.id as playlist_id from playlist left join pieces on playlist.piece_id = pieces.id where start_time > now() order by start_time limit 1";
$result = $db->query($sql);
if ($db->error) {
  echo $db->error . "\n<br />\n" . $sql;
}
if (count($result) <= 0) {
  echo json_encode(Array("error" => "Empty Playlist"));
  die;
}

$info = $result[0];

$content = file_get_contents("../content/{$info['id']}.json");
$info['payload'] = $content;

// Examine piece to determine how many parts it has
$piece = json_decode($content);
$partCount = count($piece);

// Which parts have been allocated, and do we already have one?
$sql = "select * from parts where playlist_id = '{$info['playlist_id']}'";
$parts = $db->query($sql);
if ($db->error) {
  echo $db->error . "\n<br />\n" . $sql;
}
$availableParts = Array();
for($i = 0; $i < $partCount; $i++) {
  array_push($availableParts, $i);
  $availableParts[$i] = true;
}
$assignedPart = null;
foreach($parts as $part) {
  if (!strcmp($part['ip'], $ip)) {
    $assignedPart = $part['part'];
    $info['part'] = intval($assignedPart);
  }
  
  $availableParts[intval($part['part'])] = false;
}
if ($assignedPart == null) {
  // We don't yet have a part; assign one.
  foreach($availableParts as $part => $free) {
    if ($free) {
      $assignedPart = $part;
      $sql = "insert into parts (playlist_id, ip, part) values ('{$info['playlist_id']}', '{$ip}', '{$part}')";
      $db->query($sql);
      if ($db->error) {
        echo $db->error . "\n<br />\n" . $sql;
      }
      $info['part'] = intval($part);
      break;
    }
  }
}

echo json_encode($info);
?>