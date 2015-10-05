<?php 
// set the uses array
$path = dirname(__DIR__);

// load core file
$core = array(
	$path.'/config/const.php',
	$path.'/inc/core/NotificationInvoker.php',
	$path.'/inc/core/SlackInvoker.php'
);

// count and check if uses array is valid
if (count($core) !== 0 && is_array($core) !== FALSE) {
	foreach ($core as $dependency) {
		require_once($dependency);
	}
}

// load dependencies
$dependencies = array(
	$path.'/inc/FDCWebhook.php',
);

// count and check if dependencies is valid
if (count($dependencies) !== 0 && is_array($dependencies) !== FALSE) {
	foreach ($dependencies as $dependency) {
		require_once($dependency);
	}
}