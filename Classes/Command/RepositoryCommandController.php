<?php

namespace TYPO3\ArtifactServer\Command;

/* *
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
	 * @var \TYPO3\ArtifactServer\Domain\Repository\TagRepository
	 * @FLOW3\Inject
	 */
	protected $tagRepository;

	/**
	 * @var \TYPO3\ArtifactServer\Domain\Repository\AuthorRepository
	 * @FLOW3\Inject
	 */
	protected $authorRepository;

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
		/** @var $package \TYPO3\ArtifactServer\Domain\Model\Package */
		foreach ($packages as $package) {
			$this->outputLine('Importing <b>%s</b>', array($package->getRepository()));
			$repository = new VcsRepository(array('url' => $package->getRepository()));

			$versionsFromRepository = $repository->getPackages();
			$start = new \DateTime();

			usort($versionsFromRepository, function (\Composer\Package\PackageInterface $a, \Composer\Package\PackageInterface $b) {
				return version_compare($a->getVersion(), $b->getVersion());
			});

			/** @var $versionFromRepository \Composer\Package\PackageInterface */
			foreach ($versionsFromRepository as $versionFromRepository) {
				$this->outputLine('Storing %s (%s)', array($versionFromRepository->getPrettyVersion(), $versionFromRepository->getVersion()));
				$this->updateVersionInformation($package, $versionFromRepository);
			}

			// remove outdated -dev versions
			/** @var $version \TYPO3\ArtifactServer\Domain\Model\Version */
			foreach ($package->getVersions() as $version) {
				if ($version->getDevelopment() && $version->getUpdatedAt() < $start) {
					$this->outputLine('Deleting stale version: ' . $version->getVersion());
					$this->versionRepository->remove($version);
				}
			}

			$package->setUpdatedAt(new \DateTime);
			$package->setCrawledAt(new \DateTime);
			$this->packageRepository->update($package);
		}
	}

	/**
	 * @param \TYPO3\ArtifactServer\Domain\Model\Package $package
	 * @param \Composer\Package\PackageInterface $versionFromRepository
	 * @return mixed
	 */
	protected function updateVersionInformation(\TYPO3\ArtifactServer\Domain\Model\Package $package, \Composer\Package\PackageInterface $versionFromRepository) {
		$versionExists = FALSE;
		$version = new \TYPO3\ArtifactServer\Domain\Model\Version();
		$version->setName($package->getName());
		$version->setNormalizedVersion(preg_replace('{-dev$}i', '', $versionFromRepository->getVersion()));

		// check if we have that version yet
		/** @var $existingVersion \TYPO3\ArtifactServer\Domain\Model\Version */
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

		$this->setVersionProperties($version, $versionFromRepository);
		$version->setDevelopment(substr($versionFromRepository->getVersion(), -4) === '-dev');
		$version->setPackage($package);
		if (!$package->getVersions()->contains($version)) {
			$package->addVersions($version);
		}

		if (!$package->getVersions()->contains($version)) {
			$package->addVersions($version);
		}
		$version->setUpdatedAt(new \DateTime);

		$this->setSourceAndDistTypes($version, $versionFromRepository, $package);
		$this->setTags($version, $versionFromRepository);
		$this->setAuthors($version, $versionFromRepository);
		$this->setLinksToOtherPackages($version, $versionFromRepository);

		if ($versionExists === TRUE) {
			$this->versionRepository->update($version);
		} else {
			$this->versionRepository->add($version);
		}
	}

	/**
	 * @param \TYPO3\ArtifactServer\Domain\Model\Version $version
	 * @param \Composer\Package\PackageInterface $versionFromRepository
	 */
	protected function setVersionProperties(\TYPO3\ArtifactServer\Domain\Model\Version $version, \Composer\Package\PackageInterface $versionFromRepository) {
		$propertiesToMap = array(
			'name' => 'name',
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
	}

	/**
	 * @param \TYPO3\ArtifactServer\Domain\Model\Version $version
	 * @param \Composer\Package\PackageInterface $versionFromRepository
	 * @param \TYPO3\ArtifactServer\Domain\Model\Package $package
	 */
	protected function setSourceAndDistTypes(\TYPO3\ArtifactServer\Domain\Model\Version $version, \Composer\Package\PackageInterface $versionFromRepository, \TYPO3\ArtifactServer\Domain\Model\Package $package) {
		if ($versionFromRepository->getSourceType()) {
			$source['type'] = $versionFromRepository->getSourceType();
			$source['url'] = $versionFromRepository->getSourceUrl();
			$source['reference'] = $versionFromRepository->getSourceReference();
			$version->setSource($source);
		}

		if ($versionFromRepository->getDistType()) {
			$dist['type'] = $versionFromRepository->getDistType();
			$dist['url'] = $versionFromRepository->getDistUrl();
			$dist['reference'] = $versionFromRepository->getDistReference();
			$dist['shasum'] = $versionFromRepository->getDistSha1Checksum();
			$version->setDist($dist);
		}

		if ($versionFromRepository->getType()) {
			$version->setType($versionFromRepository->getType());
			if ($versionFromRepository->getType() && $versionFromRepository->getType() !== $package->getType()) {
				$package->setType($versionFromRepository->getType());
			}
		}
	}

	/**
	 * @param \TYPO3\ArtifactServer\Domain\Model\Version $version
	 * @param \Composer\Package\PackageInterface $versionFromRepository
	 * @return void
	 */
	protected function setTags(\TYPO3\ArtifactServer\Domain\Model\Version $version, \Composer\Package\PackageInterface $versionFromRepository) {
		$version->getTags()->clear();
		if (!is_array($versionFromRepository->getKeywords())) return;

		foreach ($versionFromRepository->getKeywords() as $keyword) {
			$version->addTag($this->tagRepository->getOrCreateTagByName($keyword));
		}
	}

	/**
	 * @param \TYPO3\ArtifactServer\Domain\Model\Version $version
	 * @param \Composer\Package\PackageInterface $versionFromRepository
	 * @return void
	 */
	protected function setAuthors(\TYPO3\ArtifactServer\Domain\Model\Version $version, \Composer\Package\PackageInterface $versionFromRepository) {
		$version->getAuthors()->clear();
		if (!is_array($versionFromRepository->getAuthors()))
			return;

		foreach ($versionFromRepository->getAuthors() as $authorData) {
			$author = null;
			// skip authors with no information
			if (empty($authorData['email']) && empty($authorData['name'])) {
				continue;
			}

			if (!empty($authorData['email'])) {
				$author = $this->authorRepository->findOneByEmail($authorData['email']);
			}

			if (!$author && !empty($authorData['homepage'])) {
				$author = $this->authorRepository->findOneByNameAndHomepage($authorData['name'], $authorData['homepage']);
			}

			if (!$author) {
				$author = new \TYPO3\ArtifactServer\Domain\Model\Author();
				$this->authorRepository->add($author);
			}

			foreach (array('email', 'name', 'homepage') as $field) {
				if (isset($authorData[$field])) {
					ObjectAccess::setProperty($author, $field, $authorData[$field]);
				}
			}

			$author->setUpdatedAt(new \DateTime);
			if (!$version->getAuthors()->contains($author)) {
				$version->addAuthor($author);
			}
			if (!$author->getVersions()->contains($version)) {
				$author->addVersion($version);
			}

			$this->authorRepository->update($author);
		}
	}

	/**
	 * @param \TYPO3\ArtifactServer\Domain\Model\Version $version
	 * @param \Composer\Package\PackageInterface $versionFromRepository
	 */
	protected function setLinksToOtherPackages(\TYPO3\ArtifactServer\Domain\Model\Version $version, \Composer\Package\PackageInterface $versionFromRepository) {
		$supportedLinkTypes = array(
			'require' => 'TYPO3\ArtifactServer\Domain\Model\RequireLink',
			'conflict' => 'TYPO3\ArtifactServer\Domain\Model\ConflictLink',
			'provide' => 'TYPO3\ArtifactServer\Domain\Model\ProvideLink',
			'replace' => 'TYPO3\ArtifactServer\Domain\Model\ReplaceLink',
			'recommend' => 'TYPO3\ArtifactServer\Domain\Model\RecommendLink',
			'suggest' => 'TYPO3\ArtifactServer\Domain\Model\SuggestLink',
		);

		foreach ($supportedLinkTypes as $linkType => $linkEntityClassName) {
			$links = array();
			/** @var $link \Composer\Package\Link */
			foreach (ObjectAccess::getProperty($versionFromRepository, $linkType . 's') as $link) {
				$links[$link->getTarget()] = $link->getPrettyConstraint();
			}

			/** @var $link \TYPO3\ArtifactServer\Domain\Model\AbstractPackageLink */
			foreach (ObjectAccess::getProperty($version, $linkType) as $link) {
				// clear links that have changed/disappeared (for updates)
				if (!isset($links[$link->getPackageName()]) || $links[$link->getPackageName()] !== $link->getPackageVersion()) {
					ObjectAccess::getProperty($version, $linkType)->removeElement($link);
				} else {
					// clear those that are already set
					unset($links[$link->getPackageName()]);
				}
			}

			foreach ($links as $linkPackageName => $linkPackageVersion) {
				$link = new $linkEntityClassName;
				$link->setPackageName($linkPackageName);
				$link->setPackageVersion($linkPackageVersion);
				$version->{'add' . $linkType . 'Link'}($link);
				$link->setVersion($version);
			}
		}
	}

}

?>