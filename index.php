<?php 
$path = dirname(__FILE__); 

// load
require_once($path."/inc/inc.php");

// declare error handler
$notification = new NotificationInvoker();

// check if post is TRUE
if (isset($_POST['payload']) === FALSE) {
	$notification->writeError(array("content" => "Invalid POST data"));
	exit();
}

// get payload.
$payload = json_decode($_POST['payload']);

// declare webhook
$webhook = new FDCWebhook($payload);

// check if the branch is allowed
if ($webhook->isAllowedBranch() === FALSE) {
	echo "Branch Origin should be from ".GIT_BRANCH;
	exit();
}

// execute pull
$webhook->executePull();