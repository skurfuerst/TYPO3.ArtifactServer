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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @FLOW3\Entity
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class ReplaceLink extends AbstractPackageLink
{
    /**
     * @ORM\ManyToOne(targetEntity="TYPO3\Repository\Domain\Model\Version", inversedBy="replace")
     */
    protected $version;
}