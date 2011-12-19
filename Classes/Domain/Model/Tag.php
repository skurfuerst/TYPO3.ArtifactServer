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

namespace TYPO3\Repository\Domain\Model;

use TYPO3\FLOW3\Annotations as FLOW3;
use Doctrine\ORM\Mapping as ORM;

/**
 * @FLOW3\Entity
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class Tag
{
    
    /**
     * @ORM\Column
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="TYPO3\Repository\Domain\Model\Version", mappedBy="tags")
     */
    private $versions;

    public function __construct($name = null)
    {
        $this->name = $name;
    }

    public static function getByName($em, $name, $create = false)
    {
        try {
            $qb = $em->createQueryBuilder();
            $qb->select('t')
                ->from(__CLASS__, 't')
                ->where('t.name = ?1')
                ->setMaxResults(1)
                ->setParameter(1, $name);
            return $qb->getQuery()->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
        }
        $tag = new self($name);
        $em->persist($tag);
        return $tag;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * Add versions
     *
     * @param TYPO3\Repository\Domain\Model\Version $versions
     */
    public function addVersions(\TYPO3\Repository\Domain\Model\Version $versions)
    {
        $this->versions[] = $versions;
    }

    /**
     * Get versions
     *
     * @return Doctrine\Common\Collections\Collection $versions
     */
    public function getVersions()
    {
        return $this->versions;
    }

    public function __toString()
    {
        return $this->name;
    }
}