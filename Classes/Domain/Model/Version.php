<?php

/*
 * This file is part of Packagist.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *     Nils Adermann <naderman@naderman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TYPO3\ArtifactServer\Domain\Model;

use TYPO3\FLOW3\Annotations as FLOW3;
use Doctrine\ORM\Mapping as ORM;

/**
 * @FLOW3\Entity
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class Version {

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 * @var string
	 */
	protected $description;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $targetDir;

	/**
	 * @var array
	 */
	protected $extra = array();

	/**
	 * @var \Doctrine\Common\Collections\Collection<TYPO3\ArtifactServer\Domain\Model\Tag>
	 * @ORM\ManyToMany(inversedBy="versions")
	 */
	protected $tags;

	/**
	 * @var TYPO3\ArtifactServer\Domain\Model\Package
	 * @ORM\ManyToOne(inversedBy="versions")
	 */
	protected $package;

	/**
	 * @var string
	 */
	protected $homepage;

	/**
	 * @var string
	 */
	protected $version;

	/**
	 * @var string
	 */
	protected $normalizedVersion;

	/**
	 * @var boolean
	 */
	protected $development;

	/**
	 * @ORM\Column(type="text")
	 * @var string
	 */
	protected $license;

	/**
	 * @ORM\ManyToMany(inversedBy="versions")
	 * @var \Doctrine\Common\Collections\Collection<\TYPO3\ArtifactServer\Domain\Model\Author>
	 */
	protected $authors;

	/**
	 * @ORM\OneToMany(mappedBy="version")
	 * @var \Doctrine\Common\Collections\Collection<\TYPO3\ArtifactServer\Domain\Model\RequireLink>
	 */
	protected $require;

	/**
	 * @ORM\OneToMany(mappedBy="version")
	 * @var \Doctrine\Common\Collections\Collection<\TYPO3\ArtifactServer\Domain\Model\ReplaceLink>
	 */
	protected $replace;

	/**
	 * @ORM\OneToMany(mappedBy="version")
	 * @var \Doctrine\Common\Collections\Collection<\TYPO3\ArtifactServer\Domain\Model\ConflictLink>
	 */
	protected $conflict;

	/**
	 * @ORM\OneToMany(mappedBy="version")
	 * @var \Doctrine\Common\Collections\Collection<\TYPO3\ArtifactServer\Domain\Model\ProvideLink>
	 */
	protected $provide;

	/**
	 * @ORM\OneToMany(mappedBy="version")
	 * @var \Doctrine\Common\Collections\Collection<\TYPO3\ArtifactServer\Domain\Model\RecommendLink>
	 */
	protected $recommend;

	/**
	 * @ORM\OneToMany(mappedBy="version")
	 * @var \Doctrine\Common\Collections\Collection<\TYPO3\ArtifactServer\Domain\Model\SuggestLink>
	 */
	protected $suggest;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 * @var string
	 */
	protected $source;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 * @var string
	 */
	protected $dist;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 * @var string
	 */
	protected $autoload;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 * @var string
	 */
	protected $binaries;

	/**
	 * @var \DateTime
	 */
	protected $createdAt;

	/**
	 * @var \DateTime
	 */
	protected $updatedAt;

	/**
	 * @var \DateTime
	 */
	protected $releasedAt;

	public function __construct() {
		$this->tags = new \Doctrine\Common\Collections\ArrayCollection();
		$this->require = new \Doctrine\Common\Collections\ArrayCollection();
		$this->replace = new \Doctrine\Common\Collections\ArrayCollection();
		$this->conflict = new \Doctrine\Common\Collections\ArrayCollection();
		$this->provide = new \Doctrine\Common\Collections\ArrayCollection();
		$this->recommend = new \Doctrine\Common\Collections\ArrayCollection();
		$this->suggest = new \Doctrine\Common\Collections\ArrayCollection();
		$this->authors = new \Doctrine\Common\Collections\ArrayCollection();
		$this->createdAt = new \DateTime;
		$this->updatedAt = new \DateTime;
	}

	public function toArray() {
		$tags = array();
		foreach ($this->getTags() as $tag) {
			$tags[] = $tag->getName();
		}
		$authors = array();
		foreach ($this->getAuthors() as $author) {
			$authors[] = $author->toArray();
		}

		$data = array(
			'name' => $this->getName(),
			'description' => $this->getDescription(),
			'keywords' => $tags,
			'homepage' => $this->getHomepage(),
			'version' => $this->getVersion(),
			'license' => $this->getLicense(),
			'authors' => $authors,
			'source' => $this->getSource(),
			'time' => $this->getReleasedAt() ? $this->getReleasedAt()->format('Y-m-d\TH:i:sP') : null,
			'dist' => $this->getDist(),
			'type' => $this->getType(),
			'target-dir' => $this->getTargetDir(),
			'autoload' => $this->getAutoload(),
			'extra' => $this->getExtra(),
		);

		if ($this->getBinaries()) {
			$data['bin'] = $this->getBinaries();
		}

		$supportedLinkTypes = array(
			'require',
			'conflict',
			'provide',
			'replace',
			'recommend',
			'suggest',
		);

		foreach ($supportedLinkTypes as $linkType) {
			foreach ($this->{'get' . $linkType}() as $link) {
				$link = $link->toArray();
				$data[$linkType][key($link)] = current($link);
			}
		}

		return $data;
	}

	public function equals(Version $version) {
		return strtolower($version->getName()) === strtolower($this->getName())
				&& strtolower($version->getNormalizedVersion()) === strtolower($this->getNormalizedVersion());
	}

	/**
	 * Get id
	 *
	 * @return string $id
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * Get name
	 *
	 * @return string $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Set description
	 *
	 * @param string $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * Get description
	 *
	 * @return text $description
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Set homepage
	 *
	 * @param string $homepage
	 */
	public function setHomepage($homepage) {
		$this->homepage = $homepage;
	}

	/**
	 * Get homepage
	 *
	 * @return string $homepage
	 */
	public function getHomepage() {
		return $this->homepage;
	}

	/**
	 * Set version
	 *
	 * @param string $version
	 */
	public function setVersion($version) {
		$this->version = $version;
	}

	/**
	 * Get version
	 *
	 * @return string $version
	 */
	public function getVersion() {
		return $this->version;
	}

	/**
	 * Set normalizedVersion
	 *
	 * @param string $normalizedVersion
	 */
	public function setNormalizedVersion($normalizedVersion) {
		$this->normalizedVersion = $normalizedVersion;
	}

	/**
	 * Get normalizedVersion
	 *
	 * @return string $normalizedVersion
	 */
	public function getNormalizedVersion() {
		return $this->normalizedVersion;
	}

	/**
	 * Set license
	 *
	 * @param string $license
	 */
	public function setLicense($license) {
		if (!is_array($license))
			$license = array();
		$this->license = json_encode($license);
	}

	/**
	 * Get license
	 *
	 * @return array $license
	 */
	public function getLicense() {
		return json_decode($this->license, true);
	}

	/**
	 * Set source
	 *
	 * @param string $source
	 */
	public function setSource($source) {
		$this->source = json_encode($source);
	}

	/**
	 * Get source
	 *
	 * @return text $source
	 */
	public function getSource() {
		return json_decode($this->source, true);
	}

	/**
	 * Set dist
	 *
	 * @param string $dist
	 */
	public function setDist($dist) {
		$this->dist = json_encode($dist);
	}

	/**
	 * Get dist
	 *
	 * @return text
	 */
	public function getDist() {
		return json_decode($this->dist, true);
	}

	/**
	 * Set autoload
	 *
	 * @param string $autoload
	 */
	public function setAutoload($autoload) {
		$this->autoload = json_encode($autoload);
	}

	/**
	 * Get autoload
	 *
	 * @return text
	 */
	public function getAutoload() {
		return json_decode($this->autoload, true);
	}

	/**
	 * Set binaries
	 *
	 * @param string $binaries
	 */
	public function setBinaries($binaries) {
		$this->binaries = json_encode($binaries);
	}

	/**
	 * Get binaries
	 *
	 * @return text
	 */
	public function getBinaries() {
		return json_decode($this->binaries, true);
	}

	/**
	 * Set createdAt
	 *
	 * @param datetime $createdAt
	 */
	public function setCreatedAt($createdAt) {
		$this->createdAt = $createdAt;
	}

	/**
	 * Get createdAt
	 *
	 * @return datetime $createdAt
	 */
	public function getCreatedAt() {
		return $this->createdAt;
	}

	/**
	 * Set releasedAt
	 *
	 * @param datetime $releasedAt
	 */
	public function setReleasedAt($releasedAt) {
		$this->releasedAt = $releasedAt;
	}

	/**
	 * Get releasedAt
	 *
	 * @return datetime $releasedAt
	 */
	public function getReleasedAt() {
		return $this->releasedAt;
	}

	/**
	 * Set package
	 *
	 * @param TYPO3\ArtifactServer\Domain\Model\Package $package
	 */
	public function setPackage(Package $package) {
		$this->package = $package;
	}

	/**
	 * Get package
	 *
	 * @return TYPO3\ArtifactServer\Domain\Model\Package $package
	 */
	public function getPackage() {
		return $this->package;
	}

	/**
	 * Get tags
	 *
	 * @return Doctrine\Common\Collections\Collection $tags
	 */
	public function getTags() {
		return $this->tags;
	}

	/**
	 * Set updatedAt
	 *
	 * @param datetime $updatedAt
	 */
	public function setUpdatedAt($updatedAt) {
		$this->updatedAt = $updatedAt;
	}

	/**
	 * Get updatedAt
	 *
	 * @return datetime $updatedAt
	 */
	public function getUpdatedAt() {
		return $this->updatedAt;
	}

	/**
	 * Get authors
	 *
	 * @return Doctrine\Common\Collections\Collection
	 */
	public function getAuthors() {
		return $this->authors;
	}

	/**
	 * Set type
	 *
	 * @param string $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * Get type
	 *
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Set targetDir
	 *
	 * @param string $targetDir
	 */
	public function setTargetDir($targetDir) {
		$this->targetDir = $targetDir;
	}

	/**
	 * Get targetDir
	 *
	 * @return string
	 */
	public function getTargetDir() {
		return $this->targetDir;
	}

	/**
	 * Set extra
	 *
	 * @param array $extra
	 */
	public function setExtra($extra) {
		$this->extra = $extra;
	}

	/**
	 * Get extra
	 *
	 * @return array
	 */
	public function getExtra() {
		return $this->extra;
	}

	/**
	 * Set development
	 *
	 * @param Boolean $development
	 */
	public function setDevelopment($development) {
		$this->development = $development;
	}

	/**
	 * Get development
	 *
	 * @return Boolean
	 */
	public function getDevelopment() {
		return $this->development;
	}

	/**
	 * Add tags
	 *
	 * @param TYPO3\ArtifactServer\Domain\Model\Tag $tag
	 */
	public function addTag(\TYPO3\ArtifactServer\Domain\Model\Tag $tag) {
		$this->tags[] = $tag;
	}

	/**
	 * Add authors
	 *
	 * @param TYPO3\ArtifactServer\Domain\Model\Author $authors
	 */
	public function addAuthor(\TYPO3\ArtifactServer\Domain\Model\Author $authors) {
		$this->authors[] = $authors;
	}

	/**
	 * Add require
	 *
	 * @param TYPO3\ArtifactServer\Domain\Model\RequireLink $require
	 */
	public function addRequireLink(RequireLink $require) {
		$this->require[] = $require;
	}

	/**
	 * Get require
	 *
	 * @return Doctrine\Common\Collections\Collection
	 */
	public function getRequire() {
		return $this->require;
	}

	/**
	 * Add replace
	 *
	 * @param TYPO3\ArtifactServer\Domain\Model\ReplaceLink $replace
	 */
	public function addReplaceLink(ReplaceLink $replace) {
		$this->replace[] = $replace;
	}

	/**
	 * Get replace
	 *
	 * @return Doctrine\Common\Collections\Collection
	 */
	public function getReplace() {
		return $this->replace;
	}

	/**
	 * Add conflict
	 *
	 * @param TYPO3\ArtifactServer\Domain\Model\ConflictLink $conflict
	 */
	public function addConflictLink(ConflictLink $conflict) {
		$this->conflict[] = $conflict;
	}

	/**
	 * Get conflict
	 *
	 * @return Doctrine\Common\Collections\Collection
	 */
	public function getConflict() {
		return $this->conflict;
	}

	/**
	 * Add provide
	 *
	 * @param TYPO3\ArtifactServer\Domain\Model\ProvideLink $provide
	 */
	public function addProvideLink(ProvideLink $provide) {
		$this->provide[] = $provide;
	}

	/**
	 * Get provide
	 *
	 * @return Doctrine\Common\Collections\Collection
	 */
	public function getProvide() {
		return $this->provide;
	}

	/**
	 * Add recommend
	 *
	 * @param TYPO3\ArtifactServer\Domain\Model\RecommendLink $recommend
	 */
	public function addRecommendLink(RecommendLink $recommend) {
		$this->recommend[] = $recommend;
	}

	/**
	 * Get recommend
	 *
	 * @return Doctrine\Common\Collections\Collection
	 */
	public function getRecommend() {
		return $this->recommend;
	}

	/**
	 * Add suggest
	 *
	 * @param TYPO3\ArtifactServer\Domain\Model\SuggestLink $suggest
	 */
	public function addSuggestLink(SuggestLink $suggest) {
		$this->suggest[] = $suggest;
	}

	/**
	 * Get suggest
	 *
	 * @return Doctrine\Common\Collections\Collection
	 */
	public function getSuggest() {
		return $this->suggest;
	}

	public function __toString() {
		return $this->name . ' ' . $this->version . ' (' . $this->normalizedVersion . ')';
	}

}