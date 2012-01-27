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
	 * @var \TYPO3\ArtifactServer\Worker\Queue
	 *
	 * @FLOW3\Inject
	 */
	protected $queue;

	/**
	 * @var \TYPO3\FLOW3\Persistence\Doctrine\PersistenceManager
	 *
	 * @FLOW3\Inject
	 */
	protected $persistenceManager;

	/**
	 * @var int
	 */
	protected $memoryLimit = 52428800;

	/**
	 *
	 */
	public function listenCommand() {
		$this->outputLine('beginning to wait');
		$convert = function ($size) {
			$unit=array('b','kb','mb','gb','tb','pb');
			return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
		};
		while(1) {
			$this->outputLine('waiting for job');
			$job = $this->queue->watch('test')->reserve();
			$this->outputLine('job:' . $job->getData());
			$this->queue->delete($job);

			$memory = memory_get_usage();
			$this->outputLine('memory:' . $convert($memory) . '/' . $convert($this->memoryLimit));

			if ($memory > $this->memoryLimit) {
				$this->outputLine('exiting run due to memory limit at ' . $convert($this->memoryLimit));
			}
			usleep(10);
		}
	}

	/**
	 *
	 * @param mixed $jobData
	 */
	public function addCommand() {
		// We have to persist all data before we populate the worker que
		$this->persistenceManager->persistAll();
		$args = Array();
		foreach (func_get_args() as $arg) {
			if (!is_scalar($arg) && is_object($arg)) {
				$identifierObject = new \stdClass();
				$identifierObject->objectType = get_class($arg);
				$identifierObject->identifier = $this->persistenceManager->getIdentifierByObject($arg);
				$args[] = $identifierObject;
			} else {
				$args[] = $arg;
			}
		}
		$jobData = json_encode($args);
		$this->queue->useTube('test')->put($jobData);
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
		if ($this->response) {
			$this->response->appendContent($text);
			$this->response->send();
			$this->response->setContent('');
		}
	}


}

?>