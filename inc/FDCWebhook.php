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
		$return = $this->executeCommand('sh '.dirname(DIR).'/'.SH_DIR.'/pull.sh');

		// return for git window
		echo $return . "\n";
		
		$this->handleGitResult($return);
	}

	// handle the git result
	public function handleGitResult($result){
		// check if conflicts occured or some other error occured.
		// if errors occured
		// execute sh_commands/conflct.sh
		// append error message + files
		// execute clearConflicts.sh in sh_commands to abort merge
	}

	public function executeCommand($command){
		ob_start();
		system($command);
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}
