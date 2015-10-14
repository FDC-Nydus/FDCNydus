<?php 
require_once(dirname(DIR)."/inc/inc.php");

class FDCWebhook{
	// payload container
	private $payload =  NULL;
	private $slack = "";
	private $notification = NULL;

	// construct
	function __construct($payload = NULL){
		$this->payload = $payload;
		$this->slack = new SlackInvoker();
		$this->notification = new NotificationInvoker();
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
		$return = array('pull_request' => '');

		// execute pull command
		$fetchOrigin = $this->executeCommand('cd ' . MAIN_DIR . ' && git fetch origin ' . GIT_BRANCH. ' 2>&1');
		$return['pull_result'] = $this->executeCommand('cd ' . MAIN_DIR . ' && git reset --hard HEAD 2>&1');
		sleep(1);

		// check if FETCH_HEAD does not exist
		if (strpos($fetchOrigin, "FETCH_HEAD") !== FALSE) {
			$return['pull_result'] = $this->executeCommand('cd ' . MAIN_DIR . ' && git reset --hard FETCH_HEAD 2>&1');
		} else {
			$return['pull_result'] = "Could not load FETCH_HEAD";
		}

		// return for hook window
		echo "PULL RETURN \n";
		echo $return['pull_result'];

		// handle the git result
		#$this->handleGitResult($return);
		#$this->notification->writeError(array("content" => $return['pull_result']));
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
		$slackMessage .= $this->payload->compare;
		$slackMessage .= "```\n";

		$slackMessage .= "*SITE*";
		$slackMessage .= "```";
		$slackMessage .= SITE_NAME;
		$slackMessage .= "```";

		// slack username
		$slackUname = GIT_BRANCH_LABEL." Auto Deployment " . date('F j,Y H:i:s');

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
