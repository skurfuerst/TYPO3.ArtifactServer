<?php
namespace TYPO3\ArtifactServer\Command;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Repository".           *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;
use Composer\Repository\VcsRepository;
use \TYPO3\FLOW3\Reflection\ObjectAccess;

/**
 * Repository command controller for the TYPO3.Repository package
 *
 * @FLOW3\Scope("singleton")
 */
class RepositoryCommandController extends \TYPO3\FLOW3\MVC\Controller\CommandController {


	/**
	 * @var \TYPO3\ArtifactServer\Domain\Repository\PackageRepository
	 * @FLOW3\Inject
	 */
	protected $packageRepository;

	/**
	 * @var \TYPO3\ArtifactServer\Domain\Repository\VersionRepository
	 * @FLOW3\Inject
	 */
	protected $versionRepository;

	/**
	 * @var \Composer\Package\Version\VersionParser
	 * @FLOW3\Inject
	 */
	protected $versionParser;

	/**
	 *
	 * @param string $repositoryUri
	 */
	public function addCommand($repositoryUri) {
		$package = new \TYPO3\ArtifactServer\Domain\Model\Package();
		$package->setRepository($repositoryUri);
		$this->packageRepository->add($package);
		$this->outputLine("Added package");
	}

	/**
	 * An example command
	 *
	 * The comment of this command method is also used for FLOW3's help screens. The first line should give a very short
	 * summary about what the command does. Then, after an empty line, you should explain in more detail what the command
	 * does. You might also give some usage example.
	 *
	 * It is important to document the parameters with param tags, because that information will also appear in the help
	 * screen.
	 *
	 * @return void
	 */
	public function importCommand() {
		$packages = $this->packageRepository->findAll(); // TODO: only find the ones which have not been recently updated
		foreach ($packages as $package) {
			$this->outputLine('Importing <b>%s</b>', array($package->getRepository()));
			$repository = new VcsRepository(array('url' => $package->getRepository()));

			$versionsFromRepository = $repository->getPackages();
			$start = new \DateTime();

			usort($versionsFromRepository, function ($a, $b) {
				return version_compare($a->getVersion(), $b->getVersion());
			});

			foreach ($versionsFromRepository as $versionFromRepository) {
				$this->outputLine('Storing %s (%s)', array($versionFromRepository->getPrettyVersion(), $versionFromRepository->getVersion()));

				$this->updateVersionInformation($package, $versionFromRepository);
			}

				// remove outdated -dev versions
			foreach ($package->getVersions() as $version) {
				if ($version->getDevelopment() && $version->getUpdatedAt() < $start) {
					$this->outputLine('Deleting stale version: '.$version->getVersion());
					$this->versionRepository->remove($version);
				}
			}

			$package->setUpdatedAt(new \DateTime);
			$package->setCrawledAt(new \DateTime);
			$this->packageRepository->update($package);
		}
	}

	protected function updateVersionInformation(\TYPO3\ArtifactServer\Domain\Model\Package $package, \Composer\Package\PackageInterface $versionFromRepository) {
		$versionExists = FALSE;
		$version = new \TYPO3\ArtifactServer\Domain\Model\Version();
		$version->setName($package->getName());
		$version->setNormalizedVersion(preg_replace('{-dev$}i', '', $versionFromRepository->getVersion()));

		// check if we have that version yet
        foreach ($package->getVersions() as $existingVersion) {
            if ($existingVersion->equals($version)) {
                // avoid updating newer versions, in case two branches have the same version in their composer.json
                if ($existingVersion->getReleasedAt() > $versionFromRepository->getReleaseDate()) {
                    return;
                }
                if ($existingVersion->getDevelopment()) {
					$versionExists = TRUE;
                    $version = $existingVersion;
                    break;
                }
                return;
            }
        }

		$propertiesToMap = array(
			'prettyVersion' => 'version',
			'description' => 'description',
			'homepage' => 'homepage',
			'license' => 'license',
			'releaseDate' => 'releasedAt',
			'targetDir' => 'targetDir',
			'autoload' => 'autoload',
			'extra' => 'extra',
			'binaries' => 'binaries'
		);
		foreach ($propertiesToMap as $sourcePropertyName => $targetPropertyName) {
			$value = ObjectAccess::getProperty($versionFromRepository, $sourcePropertyName);
			ObjectAccess::setProperty($version, $targetPropertyName, $value);
		}

        $version->setDevelopment(substr($versionFromRepository->getVersion(), -4) === '-dev');
		$version->setPackage($package);
		if (!$package->getVersions()->contains($version)) {
			$package->addVersions($version);
		}
		$version->setUpdatedAt(new \DateTime);

		// TODO: source type, dist type, type
		// TODO: tags
		// TODO: authors
		// TODO: supportedLinkTypes

		if ($versionExists === TRUE) {
			$this->versionRepository->update($version);
		} else {
			$this->versionRepository->add($version);
		}

	}

}

?>