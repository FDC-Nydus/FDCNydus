<?php 
require_once(dirname(DIR)."/inc/inc.php");

class FDCWebhook{
	// payload container
	private $payload =  NULL;
	public static $slack = "";
	public $jsonData = "";
	public $jsonFile = "";
	public $attempts = 1;

	// construct
	function __construct($payload = NULL){
		$this->payload = $payload;
		$this->slack = new SlackInvoker();
		$this->jsonData = new stdClass(); 
		$this->jsonData->status = 0;
		$this->jsonData->commit = 0;
		$this->jsonData->timestamp = 0;
		$this->jsonFile = dirname(DIR) . JSON_FILE;
	}

	// check if branch is allowed
	public function isAllowedBranch(){
		// checker
		$check = false;
		// check if the payload with ref exists
		if (isset($this->payload->ref) && strpos($this->payload->ref, GIT_BRANCH)) {
			$check = true;
		}
		// return the checker
		return $check;
	}

	// pull from dev branch
	public function executePull(){
		// set return array
		$return = array('pull_result' => '');

		// fetch data
		$jsonData = $this->getJsonData();

		//try executing
		try {
			// check if git is still processing anything
			if ($this->isProcessing() === FALSE) {
				// set the process to active
				$this->setProcess();

				// execute pull command
				$return['pull_result'] = $this->executeCommand('cd ' . MAIN_DIR . ' && git reset --hard ' . GIT_BRANCH_REF . ' 2>&1');

				// handle the git result
				$this->handleGitResult($return);

				// refetch data
				$jsonData = $this->getJsonData();

				// decrement process
				$jsonData->status = 0;
			} else {
				$jsonData->status = isset($jsonData->status) ? 1 : 0;
				$return['pull_result'] = "Max attempts (" . MAX_ATTEMPTS . ") reached!\n";
			}

			// echo for hook window
			echo "PULL RETURN (" . $this->attempts . ")\n";
			echo $return['pull_result'];

			// overwrite file
			$this->overWriteFile($this->jsonFile, json_encode($jsonData));

			// renew json data from json file
			$jsonData = $this->getJsonData();

			// check jsondata
			if ($jsonData->status > 0 && $this->attempts < MAX_ATTEMPTS) {
				// execute again after 1 scond
				sleep(1);
				$this->attempts++;
				$this->executePull();
				echo "\n";
			} else if ($this->attempts >= MAX_ATTEMPTS) {
				$this->handleGitResult($return);
			} else {}
		} catch (Exception $e) {
			$jsonData->status = 0;
			$this->overWriteFile($this->jsonFile, json_encode($jsonData));
		}
	}

	// pull is processing
	// @return true | false = not processing : true = processing
	public function isProcessing(){
		// check if the json file exists
		if (is_file($this->jsonFile) === FALSE) {
			// TODO : return error here
			echo "Data.json file not found";
			exit();
		}

		// fetch data
		$jsonData = $this->getJsonData();

		// check if active or not
		if ($jsonData->status <= 0) {
			return false;
		} else {
			return true;
		}
	}

	// get json data
	public function getJsonData(){
		$content = $this->readFile($this->jsonFile);
		$data = json_decode($content);
		return $data;
	}	

	// set the process
	public function setProcess($status = 1){
		$this->jsonData->status = $status;
		$this->jsonData->commit = 0;
		$this->jsonData->timestamp = time();
		$this->overWriteFile($this->jsonFile, json_encode($this->jsonData));
	}
		
	// write to data json file
	public function overWriteFile($file, $text){
		$fileWrite = fopen($file, "w");
		file_put_contents($file, $text, FILE_APPEND);
		fclose($fileWrite);
	}

	// read file
	public function readFile($file){
		$fileWrite = file_get_contents($file);
		return $fileWrite;
	}
	
	// handle the git result
	public function handleGitResult($result){
		// contains the message for slack
		$slackMessage = "";
		$slackMessage .= "*PULL RESULT*";
		$slackMessage .= "```";
		$slackMessage .= $result['pull_result'];
		$slackMessage .= "```\n";

		$slackMessage .= "*COMMIT LINK*";
		$slackMessage .= "```";
		$slackMessage .= @$this->payload->compare;
		$slackMessage .= "```\n";

		$slackMessage .= "*SITE*";
		$slackMessage .= "```";
		$slackMessage .= SITE_NAME;
		$slackMessage .= "```\n";

		$slackMessage .= "*User*";
		$slackMessage .= "```";
		$slackMessage .= @$this->payload->head_commit->author->name;
		$slackMessage .= "```";

		// slack username
		$slackUname = GIT_BRANCH_LABEL." Auto Deployment " . date('F j, Y H:i:s');

		// set the slack username
		$this->slack->username = $slackUname;
			
		// set the slack message
		$this->slack->text = $slackMessage;

		// send slack message
		$this->slack->sendSlack();
	}

	// execute command
	public function executeCommand($command){
		ob_start();
		system($command);
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}
