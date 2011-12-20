<?php

namespace TYPO3\ArtifactServer\Command;

/* *
 * This script belongs to the FLOW3 package "TYPO3.Repository".           *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Worker command controller for the TYPO3.Repository package
 *
 * @FLOW3\Scope("singleton")
 */
class WorkerCommandController extends \TYPO3\FLOW3\MVC\Controller\CommandController {

	/**
	 * @var \Pheanstalk
	 */
	protected  $pheanstalk;

	/**
	 * Constructs the controller
	 *
	 */
	public function __construct() {
		parent::__construct();
		// Start beanstalk with "beanstalkd -d -l 127.0.0.1 -p 11300" command
		$this->pheanstalk = new \Pheanstalk('127.0.0.1');
	}

	/**
	 *
	 */
	public function listenCommand() {
		$this->outputLine('beginning to wait');
		while(1) {
			$this->outputLine('waiting for job');
			$job = $this->pheanstalk->watch('test')->reserve();
			$this->outputLine('job:' . $job->getData());
			$this->pheanstalk->delete($job);

			$memory = memory_get_usage();
			$this->outputLine('memory:' . $memory);

			if ($memory > 1000000) {
				$this->outputLine('exiting run due to memory limit');
			}
			usleep(10);
		}
	}

	/**
	 *
	 * @param string $jobData
	 */
	public function addCommand($jobData) {
		$this->pheanstalk->useTube('test')->put($jobData);
		$this->outputLine("pushed: " . $jobData);
	}

	/**
	 * Outputs specified text to the console window
	 * You can specify arguments that will be passed to the text via sprintf
	 * @see http://www.php.net/sprintf
	 *
	 * @param string $text Text to output
	 * @param array $arguments Optional arguments to use for sprintf
	 * @return void
	 */
	protected function output($text, array $arguments = array()) {
		if ($arguments !== array()) {
			$text = vsprintf($text, $arguments);
		}
		$this->response->appendContent($text);
		$this->response->send();
		$this->response->setContent('');
	}


}

?>