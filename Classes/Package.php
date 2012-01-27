<?php
namespace TYPO3\ArtifactServer;

use \TYPO3\FLOW3\Package\Package as BasePackage;
use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Package base class of the TYPO3.Repository package.
 *
 * @FLOW3\Scope("singleton")
 */
class Package extends BasePackage {
	public function boot(\TYPO3\FLOW3\Core\Bootstrap $bootstrap) {
		include_once($this->getResourcesPath() . '/Private/PHP/Pheanstalk/pheanstalk_init.php');

		$dispatcher = $bootstrap->getSignalSlotDispatcher();
		$dispatcher->connect('TYPO3\ArtifactServer\Command\RepositoryCommandController', 'packageAdded', 'TYPO3\ArtifactServer\Command\WorkerCommandController', 'addCommand');
		$dispatcher->connect('TYPO3\ArtifactServer\Command\RepositoryCommandController', 'packageUpdated', 'TYPO3\ArtifactServer\Command\WorkerCommandController', 'addCommand');
		$dispatcher->connect('TYPO3\ArtifactServer\Command\RepositoryCommandController', 'packageVersionAdded', 'TYPO3\ArtifactServer\Command\WorkerCommandController', 'addCommand');
		$dispatcher->connect('TYPO3\ArtifactServer\Command\RepositoryCommandController', 'packageVersionUpdated', 'TYPO3\ArtifactServer\Command\WorkerCommandController', 'addCommand');
		$dispatcher->connect('TYPO3\ArtifactServer\Command\RepositoryCommandController', 'packageVersionRemoved', 'TYPO3\ArtifactServer\Command\WorkerCommandController', 'addCommand');
	}
}
?>