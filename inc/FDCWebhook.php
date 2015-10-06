<?php 
require_once(dirname(DIR)."/inc/inc.php");

class FDCWebhook{
	// payload container
	private $payload =  NULL;

	// construct
	function __construct($payload = NULL){
		$this->payload = $payload;
		self::$slack = new SlackInvoker();
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
		$return = array();

		// execute pull command
		$this->executeCommand('cd /vagrant/workspace/FDCDevRepo && git fetch origin master');
		$return['pull_result'] = $this->executeCommand('cd /vagrant/workspace/FDCDevRepo && git reset --hard FETCH_HEAD 2>&1');
		$this->executeCommand('cd /vagrant/workspace/FDCDevRepo && git clean -df');

		// get list of files to be pushed
		$return['file_list'] = $this->executeCommand('cd /vagrant/workspace/FDCDevRepo && git diff --stat');

		// return for hook window
		echo "PULL RETURN \n";
		echo $return['pull_result']. "\n";
		echo "CHANGED FILES \n";
		echo $return['file_list']. "\n";

		// handle the git result
		$this->handleGitResult($return);
	}

	// handle the git result
	public function handleGitResult($result){
		// check if conflicts occured or some other error occured.
		// in the result string, for "pull", status_code=0 = success, status_code=1 = fail, status_code=<anything else> = we'll assume as fail
		// if errors occured
		// execute abortMerge.sh in sh_commands to return the branch to its state before the merge
		
		// contains the message for slack
		$slackMessage = "";

		/*// if something went wrong, abort merge
		if (strpos($result, "status_code=0") === FALSE) {
			// abort merging
			$abort = $this->executeCommand('sh ' . dirname(DIR) . '/' . SH_DIR . '/' . SH_CLEAR_CONFLICT);
			
			$errData = array(
					'subject' => 'Merge Error',
					'content' => $result
			);
			//$this->notification->writeError($errData);
			// return for hook window
			echo "ABORT MESSAGE \n";
			echo $abort . "\n";
		}*/

		// slack message construction
		$slackMessage .= "```";
		$slackMessage .= "Date: ".date('Y/m/d H:i:s');
		$slackMessage .= "LIST OF FILES CHANGED";
		$slackMessage .= $result;
		$slackMessage .= "```";

		// slack username
		$slackUname = GIT_BRANCH_LABEL." auto deployment ".date("Y-m-d H:i:s");

			self::$slack->sendSlack($slackMessage);
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
