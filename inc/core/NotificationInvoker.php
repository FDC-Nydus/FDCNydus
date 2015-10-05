<?php 
class NotificationInvoker{
	public static $slack = "";

	// construct file
	function __construct(){
		self::$slack = new SlackInvoker();
	}

	// write log file on error
	public function writeError($err = FALSE){
		// check if err is valid
		if ($err == FALSE) {
			return false;
		}
		
		// check if content contains anything
		if (isset($err['content'])) {
			// create log file
			$filename = "/logs/error_log.log";

			// construct message
			$errMsg = "[".date('Y/m/d H:i:s')."] ";
			$errMsg .= isset($error['subject']) ? $err['subject']."-" : "";
			$errMsg .= $err['content'];
			$errMsg .= "\n";

			// open directory
			fopen(dirname(DIR).$filename, "a");
			file_put_contents(dirname(DIR).$filename, $errMsg, FILE_APPEND);

			// push to slack
			self::$slack->sendSlack();
		}
	}
}
