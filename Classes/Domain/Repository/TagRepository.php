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
class TagRepository extends \TYPO3\FLOW3\Persistence\Doctrine\Repository {
	public function getOrCreateTagByName($name) {
		$tag = $this->findOneByName($name);
		if ($tag === NULL) {
			$tag = new TYPO3\ArtifactServer\Domain\Model\Tag($name);
			$this->add($tag);
		}
		return $tag;
	}
}
