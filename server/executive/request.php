<?php
require_once('../../config/config.php');
require_once('../Database.php');
require_once('../Session.php');
require_once('../Request.php');
require_once('Event.php');

$db = new Database();
$executive = new Executive($db);
$post = $_POST;
$return = array();

if (Request::post("add_executive")) {
		$return["executive"] = $executive->addEvent($post);
}
if (Request::post("get_executives")) {
	$return["executive"] = $executive->getEvents($post);
}
echo json_encode($return);

?>