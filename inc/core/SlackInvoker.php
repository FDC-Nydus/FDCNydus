<?php
class SlackInvoker {
	// public $timeout  = 5; //set timeout 
	public static $channel  = "#nc-deploy"; //set the default channel
	// public static $text     = NULL; //send text
	public static $token    = "xoxp-3193633639-5180400243-5161824998-602ce1"; //set token
	public static $username = "bot";

	// construct
	function __construct(){
	}

	public static function sendSlack(){
		$token = $this->token;
		
		$slack = "https://slack.com/api/chat.postMessage";
		$slack .= "?token=".$this->token;
		$slack .= "&channel=".urlencode($this->channel);
		$slack .= "&text=".urlencode($text);
		$slack .= "&username=".urlencode($this->username);
		
		$cookie = tempnam ("/tmp", "CURLCOOKIE");
		$ch     = curl_init();
		$headers = array();
		$headers[] = 'Content-type: charset=utf-8'; 
		$headers[] = 'Connection: Keep-Alive';

		curl_setopt($ch, CURLOPT_URL, $slack);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    # required for https urls
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		
	  $content = curl_exec($ch);
	  $response = curl_getinfo($ch);
		curl_error($ch);
	  curl_close ($ch);
	}
}