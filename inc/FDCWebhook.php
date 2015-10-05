<?php 
class FDCWebhook{
	// payload container
	private $payload =  NULL;

	// construct
	function __construct($payload){
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
		$return = shell_exec('sh '.dirname(DIR).'/'.SH_DIR.'/pullDev.sh');
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
}
