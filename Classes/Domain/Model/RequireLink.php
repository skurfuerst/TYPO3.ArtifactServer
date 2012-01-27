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
class RequireLink extends AbstractPackageLink {

	/**
	 * @var \TYPO3\ArtifactServer\Domain\Model\Version
	 * @ORM\ManyToOne(inversedBy="require")
	 */
	protected $version;

}