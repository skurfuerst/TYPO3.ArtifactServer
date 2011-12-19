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
 * @ORM\MappedSuperclass()
 * @FLOW3\Entity
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
abstract class AbstractPackageLink
{

    /**
     * @ORM\Column()
     */
    protected $packageName;

    /**
     * @ORM\Column()
     */
    protected $packageVersion;

    public function toArray()
    {
        return array($this->getPackageName() => $this->getPackageVersion());
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Set packageName
     *
     * @param string $packageName
     */
    public function setPackageName($packageName)
    {
        $this->packageName = $packageName;
    }

    /**
     * Get packageName
     *
     * @return string
     */
    public function getPackageName()
    {
        return $this->packageName;
    }

    /**
     * Set packageVersion
     *
     * @param string $packageVersion
     */
    public function setPackageVersion($packageVersion)
    {
        $this->packageVersion = $packageVersion;
    }

    /**
     * Get packageVersion
     *
     * @return string
     */
    public function getPackageVersion()
    {
        return $this->packageVersion;
    }

    /**
     * Set version
     *
     * @param TYPO3\ArtifactServer\Domain\Model\Version $version
     */
    public function setVersion(\TYPO3\ArtifactServer\Domain\Model\Version $version)
    {
        $this->version = $version;
    }

    /**
     * Get version
     *
     * @return TYPO3\ArtifactServer\Domain\Model\Version
     */
    public function getVersion()
    {
        return $this->version;
    }

    public function __toString()
    {
        return $this->packageName.' '.$this->packageVersion;
    }
}
