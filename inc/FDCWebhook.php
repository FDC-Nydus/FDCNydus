<?php 
class FDCWebhook{
	// payload container
	private $payload =  NULL;

	// construct
	function __construct($payload = NULL){
		$this->payload = $payload;
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
		// execute pull command
		$return = $this->executeCommand('sh ' . dirname(DIR) . '/' . SH_DIR . '/' . SH_PULL);

		// return for git window
		echo $return . "\n";
			
		// handle the git result
		$this->handleGitResult($return);
	}

	// handle the git result
	public function handleGitResult($result){
		// check if conflicts occured or some other error occured.
		// in the result string, for "pull", status_code=0 = success, status_code=1 = fail, status_code=<anything else> = we'll assume as fail
		// if errors occured
		// execute sh_commands/conflct.sh
		// append error message + files
		// execute clearConflicts.sh in sh_commands to abort merge
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
