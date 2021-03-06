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

namespace TYPO3\ArtifactServer\Domain\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class PackageRepository extends \TYPO3\FLOW3\Persistence\Doctrine\Repository
{
	protected static $ENTITY_CLASSNAME = '\TYPO3\ArtifactServer\Domain\Model\Package';
    /*public function getStalePackages()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('p, v')
            ->from('TYPO3\ArtifactServer\Domain\Model\Package', 'p')
            ->leftJoin('p.versions', 'v')
            ->where('p.crawledAt IS NULL OR p.crawledAt < ?0')
            ->setParameters(array(new \DateTime('-1hour')));
        return $qb->getQuery()->getResult();
    }

    public function getStalePackagesForIndexing()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('p, v, t')
            ->from('TYPO3\ArtifactServer\Domain\Model\Package', 'p')
            ->leftJoin('p.versions', 'v')
            ->leftJoin('v.tags', 't')
            ->where('p.indexedAt IS NULL OR p.indexedAt < ?0')
            ->setParameters(array(new \DateTime('-1hour')));
        return $qb->getQuery()->getResult();
    }

    public function findOneByName($name)
    {
        $qb = $this->getBaseQueryBuilder()
            ->where('p.name = ?0')
            ->setParameters(array($name));
        return $qb->getQuery()->getSingleResult();
    }

    public function findByTag($name)
    {
        return $this->getBaseQueryBuilder()
            // eliminate maintainers & tags from the select, because of the groupBy
            ->select('p, v')
            ->where('t.name = ?0')
            ->setParameters(array($name));
    }

    public function getQueryBuilderByMaintainer(User $user)
    {
        $qb = $this->getBaseQueryBuilder()
            // eliminate maintainers & tags from the select, because of the groupBy
            ->select('p, v')
            ->where('m.id = ?0')
            ->setParameters(array($user->getId()));
        return $qb;
    }

    public function getBaseQueryBuilder()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('p, v, t, m')
            ->from('TYPO3\ArtifactServer\Domain\Model\Package', 'p')
            ->leftJoin('p.versions', 'v')
            ->leftJoin('p.maintainers', 'm')
            ->leftJoin('v.tags', 't')
            ->orderBy('v.development', 'DESC')
            ->addOrderBy('v.releasedAt', 'DESC');
        return $qb;
    }*/
	/**
	 * @param \TYPO3\ArtifactServer\Domain\Model\Package $package
	 */
	public function add($package) {
		$isRepositoryAlreadyPresentQuery = $this->createQuery();
		if ($isRepositoryAlreadyPresentQuery->matching($isRepositoryAlreadyPresentQuery->equals('repository', $package->getRepository()))->execute()->count() > 0) {
			throw new \TYPO3\FLOW3\Cache\Exception\DuplicateIdentifierException('A package with the same repository url already exists.', 1327653290);
		} else {
			parent::add($package);
		}
	}


}
