<?php
require_once('../../config/config.php');
require_once('../Database.php');
require_once('../Session.php');
require_once('../Request.php');
require_once('Event.php');
session_start();
$db = new Database();
$event = new Event($db);
$return = array();
$post = $_POST;
$return["session"] = $_SESSION;
$return["post"] = $_POST;
if (empty($_SESSION)) {
	exit();
}
if (Request::post("add_event")) {
		$return["event"] = $event->addEvent($post);
}
if (Request::post("update_event")) {
	$return["event"] = $event->updateEvent($post);
}
if (Request::post("delete_event")) {
	$return["event"] = $event->deleteEvent($post);
}
if (Request::post("get_events")) {
	$return["event"]["current"] = $event->getActiveEvents($post);
}
if (Request::post("get_past_events")) {
	$return["event"]["past"] = $event->getPastEvents($post);
}
echo json_encode($return);

?>