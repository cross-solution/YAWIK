<?php

/**
 * YAWIK
 *
 * @see       https://github.com/cross-solution/YAWIK for the canonical source repository
 * @copyright https://github.com/cross-solution/YAWIK/blob/master/COPYRIGHT
 * @license   https://github.com/cross-solution/YAWIK/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Organizations\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * TODO: description
 *
 * @ODM\EmbeddedDocument
 *
 * @author Mathias Gelhausen
 * TODO: write tests
 */
class FeaturedCompanySettings
{
    /**
     * @ODM\Field(type="bool")
     * @var bool
     */
    private $isFeaturedCompany = false;

    /**
     * @ODM\Field(type="int")
     * @var int
     */
    private $priority = 1;
}
