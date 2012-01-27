<?php

namespace TYPO3\ArtifactServer\Worker;

/* *
 * This script belongs to the FLOW3 package "TYPO3.Repository".           *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;
use Pheanstalk;

/**
 * Queue Manager for a beanstalk queue
 *
 * @FLOW3\Scope("singleton")
 */
class Queue {

	/**
	 * @var \Pheanstalk
	 */
	protected $pheanstalk;

	/**
	 * @param string $host
	 * @param int $port
	 * @param int $connectTimeout
	 */
	public function __construct($host, $port = Pheanstalk::DEFAULT_PORT, $connectTimeout = null) {
		$this->pheanstalk = new Pheanstalk($host, $port, $connectTimeout);
	}

	/**
	 * @param $connection
	 * @return \Pheanstalk
	 */
	public function setConnection($connection) {
		return $this->pheanstalk->setConnection($connection);
	}

	/**
	 * @param $job
	 * @param int $priority
	 */
	public function bury($job, $priority = Pheanstalk::DEFAULT_PRIORITY) {
		$this->pheanstalk->bury($job, $priority);
	}

	/**
	 * @param $job
	 * @return \Pheanstalk
	 */
	public function delete($job) {
		return $this->pheanstalk->delete($job);
	}

	/**
	 * @param $tube
	 * @return \Pheanstalk
	 */
	public function ignore($tube) {
		return $this->pheanstalk->ignore($tube);
	}

	/**
	 * @param $max
	 * @return int
	 */
	public function kick($max) {
		return $this->pheanstalk->kick($max);
	}

	/**
	 * @return array
	 */
	public function listTubes() {
		return $this->pheanstalk->listTubes();
	}

	/**
	 * @param bool $askServer
	 * @return array
	 */
	public function listTubesWatched($askServer = false) {
		return $this->pheanstalk->listTubesWatched($askServer);
	}

	/**
	 * @param bool $askServer
	 * @return string
	 */
	public function listTubeUsed($askServer = false) {
		return $this->pheanstalk->listTubeUsed($askServer);
	}

	/**
	 * @param $tube
	 * @param $delay
	 * @return \Pheanstalk
	 */
	public function pauseTube($tube, $delay) {
		return $this->pheanstalk->pauseTube($tube, $delay);
	}

	/**
	 * @param $jobId
	 * @return object|\Pheanstalk_Job
	 */
	public function peek($jobId) {
		return $this->pheanstalk->peek($jobId);
	}

	/**
	 * @param null $tube
	 * @return object|\Pheanstalk_Job
	 */
	public function peekReady($tube = null) {
		return $this->pheanstalk->peekReady($tube);
	}

	/**
	 * @param null $tube
	 * @return object|\Pheanstalk_Job
	 */
	public function peekDelayed($tube = null) {
		return $this->pheanstalk->peekDelayed($tube);
	}

	/**
	 * @param null $tube
	 * @return object|\Pheanstalk_Job
	 */
	public function peekBuried($tube = null) {
		return $this->pheanstalk->peekBuried($tube);
	}

	/**
	 * @param $data
	 * @param int $priority
	 * @param int $delay
	 * @param int $ttr
	 * @return int
	 */
	public function put($data, $priority = Pheanstalk::DEFAULT_PRIORITY, $delay = Pheanstalk::DEFAULT_DELAY, $ttr = Pheanstalk::DEFAULT_TTR) {
		return $this->pheanstalk->put($data, $priority, $delay, $ttr);
	}

	/**
	 * @param $tube
	 * @param $data
	 * @param int $priority
	 * @param int $delay
	 * @param int $ttr
	 * @return int
	 */
	public function putInTube($tube, $data, $priority = Pheanstalk::DEFAULT_PRIORITY, $delay = Pheanstalk::DEFAULT_DELAY, $ttr = Pheanstalk::DEFAULT_TTR) {
		return $this->pheanstalk->putInTube($tube, $data, $priority, $delay, $ttr);
	}

	/**
	 * @param $job
	 * @param int $priority
	 * @param int $delay
	 * @return \Pheanstalk
	 */
	public function release($job, $priority = Pheanstalk::DEFAULT_PRIORITY, $delay = Pheanstalk::DEFAULT_DELAY) {
		return $this->pheanstalk->release($job, $priority, $delay);
	}

	/**
	 * @param null $timeout
	 * @return object|\Pheanstalk_Job
	 */
	public function reserve($timeout = null) {
		return $this->pheanstalk->reserve($timeout);
	}

	/**
	 * @param $tube
	 * @param null $timeout
	 * @return object|\Pheanstalk_Job
	 */
	public function reserveFromTube($tube, $timeout = null) {
		return $this->pheanstalk->reserveFromTube($tube, $timeout);
	}

	/**
	 * @param $job
	 * @return object
	 */
	public function statsJob($job) {
		return $this->pheanstalk->statsJob($job);
	}

	/**
	 * @param $tube
	 * @return object
	 */
	public function statsTube($tube) {
		return $this->pheanstalk->statsTube($tube);
	}

	/**
	 * @return object
	 */
	public function stats() {
		return $this->pheanstalk->stats();
	}

	/**
	 * @param $job
	 * @return \Pheanstalk
	 */
	public function touch($job) {
		return $this->pheanstalk->touch($job);
	}

	/**
	 * @param $tube
	 * @return \Pheanstalk
	 */
	public function useTube($tube) {
		return $this->pheanstalk->useTube($tube);
	}

	/**
	 * @param $tube
	 * @return \Pheanstalk
	 */
	public function watch($tube) {
		return $this->pheanstalk->watch($tube);
	}

	/**
	 * @param $tube
	 * @return \Pheanstalk
	 */
	public function watchOnly($tube) {
		return $this->pheanstalk->watchOnly($tube);
	}

}

?>