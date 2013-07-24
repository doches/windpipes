<?php

require_once("../lib/database.php");
require_once("../lib/response.php");
require_once("../lib/tokens.php");

function listTemplate($sql, $xml_tag, $plural) {
  global $db;
  $all_items = $db->query($sql);
  emitErrorIf(ErrorResponse::OperationFail, $db->error);
  
  $items = Array();
  foreach($all_items as $item) {
    $tag = Array("__xml_tag" => $xml_tag);
    foreach($item as $key => $value) {
      $tag[$key] = $value;
    }
    array_push($items, $tag);
  }
  
  $response = new OKResponse();
  $response->set($plural, $items);
  $response->output();
}

$db = new Database();
checkToken($db);

$account_id = intval($_REQUEST['account_id']);

?>