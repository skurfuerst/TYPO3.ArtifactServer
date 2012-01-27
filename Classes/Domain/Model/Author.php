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
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @FLOW3\Entity
 */
class Author {

	/**
	 * Name of author
	 *
	 * @ORM\Column(nullable=true)
	 * @var string
	 */
	protected $name;

	/**
	 * @ORM\Column(nullable=true)
	 * @var string
	 */
	protected $email;

	/**
	 * @ORM\Column(nullable=true)
	 * @var string
	 */
	protected $homepage;

	/**
	 * @var \Doctrine\Common\Collections\Collection<\TYPO3\ArtifactServer\Domain\Model\Version>
	 * @ORM\ManyToMany(mappedBy="authors")
	 */
	protected $versions;

	/**
	 * @var \DateTime
	 */
	protected $createdAt;

	/**
	 * @var \DateTime
	 * @ORM\Column(nullable=true)
	 */
	protected $updatedAt;

	/**
	 *
	 */
	public function __construct() {
		$this->versions = new ArrayCollection();
		$this->createdAt = new \DateTime;
	}

	/**
	 * @return array
	 */
	public function toArray() {
		return array(
			'name' => $this->getName(),
			'email' => $this->getEmail(),
			'homepage' => $this->getHomepage(),
		);
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
	 * Set createdAt
	 *
	 * @param \DateTime $createdAt
	 */
	public function setCreatedAt($createdAt) {
		$this->createdAt = $createdAt;
	}

	/**
	 * Get createdAt
	 *
	 * @return \DateTime $createdAt
	 */
	public function getCreatedAt() {
		return $this->createdAt;
	}

	/**
	 * Add versions
	 *
	 * @param TYPO3\ArtifactServer\Domain\Model\Version $version
	 */
	public function addVersion(Version $version) {
		$this->versions[] = $version;
	}

	/**
	 * Get versions
	 *
	 * @return \Doctrine\Common\Collections\Collection $versions
	 */
	public function getVersions() {
		return $this->versions;
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
	 * Set email
	 *
	 * @param string $email
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * Get email
	 *
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
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
	 * @return string
	 */
	public function getHomepage() {
		return $this->homepage;
	}
}