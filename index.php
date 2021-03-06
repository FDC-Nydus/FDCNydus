<?php 
$path = dirname(__FILE__); 

// load
require_once($path."/inc/inc.php");

// declare error handler
$notification = new NotificationInvoker();

// check if post is TRUE
if (isset($_POST['payload']) === FALSE) {
	echo "Invalid POST data";
	$notification->writeError(array("content" => "Invalid POST data"));
	exit();
}

// get payload.
$payload = json_decode(@$_POST['payload']);

// get post size
$postSize = (int) @$_SERVER['CONTENT_LENGTH'];

// show post size
echo "Post Size : " . $postSize . "\n";

// declare webhook
$webhook = new FDCWebhook(@$payload);

// check if the branch is allowed
if ($webhook->isAllowedBranch() === FALSE) {
	echo "Branch Origin should be from ".GIT_BRANCH_LABEL;
	$notification->writeError(array("content" => "Branch Origin should be from ".GIT_BRANCH_LABEL));
	exit();
}

// execute pull
$webhook->executePull();
$notification->writeError(array("content" => "executed pull"));
# test lang
