<?php
require_once ('../../config/config.php');
require_once ('../../config/twitter.php');
require_once ('../Database.php');
require_once ('../Session.php');
require_once ('../Request.php');
require_once ('TwitterAPIExchange.php');
require_once ('TwitterAPI.php');
session_start ();
$twitter = new TwitterAPI();
$post = $_POST;
$return["post"] = $_POST;
if (Request::post("statuses_update")) {
	$return["status"] = $twitter->statusesUpdate($post);
}
if (Request::post("statuses_user_timeline")) {
	$return["status"] = $twitter->statusesUserTimeline($post);
}
echo json_encode($return);
?>