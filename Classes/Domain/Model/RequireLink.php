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

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="link_require")
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class RequireLink extends PackageLink
{
    /**
     * @ORM\ManyToOne(targetEntity="TYPO3\Repository\Domain\Model\Version", inversedBy="require")
     */
    protected $version;
}