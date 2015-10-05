<?php 
class ErrorHandler{
	private $const = NULL;

	function __construct(){
		$this->const = new ConstVars; 
	}

	// write log file on error
	public function writeError($err = FALSE){
		// check if err is valid
		if ($err == FALSE) {
			return false;
		}
		
		if (strpos($err['content'], "CONTENT") !== FALSE) {
			$filename = "error_log.txt";

			$errMsg = "[".date('Y/m/d H:i:s')."] ";
			$errMsg .= "Subject: ".$err['subject']." ";
			$errMsg .= $err['content'];

			fopen($this->const->config['DIR'].$filename, "a");
			file_put_contents($filename, $errMsg, FILE_APPEND);
		}
	}
}
