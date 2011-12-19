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
 * @FLOW3\Entity
 */
class User
{
    
    /**
     * @ORM\ManyToMany(targetEntity="Package", mappedBy="maintainers")
     */
    protected $packages;

    /**
     * @ORM\OneToMany(targetEntity="TYPO3\ArtifactServer\Domain\Model\Author", mappedBy="owner")
     */
    protected $authors;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    public function __construct()
    {
        $this->packages = new ArrayCollection();
        $this->authors = new ArrayCollection();
        $this->createdAt = new \DateTime();
        parent::__construct();
    }

    public function toArray()
    {
        return array(
            'name' => $this->getUsername(),
            'email' => $this->getEmail(),
        );
    }

    /**
     * Add packages
     *
     * @param TYPO3\ArtifactServer\Domain\Model\Package $packages
     */
    public function addPackages(Package $packages)
    {
        $this->packages[] = $packages;
    }

    /**
     * Get packages
     *
     * @return Doctrine\Common\Collections\Collection $packages
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * Add authors
     *
     * @param TYPO3\ArtifactServer\Domain\Model\Author $authors
     */
    public function addAuthors(\TYPO3\ArtifactServer\Domain\Model\Author $authors)
    {
        $this->authors[] = $authors;
    }

    /**
     * Get authors
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * Set createdAt
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}