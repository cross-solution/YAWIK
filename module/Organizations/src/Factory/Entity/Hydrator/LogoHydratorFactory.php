<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Organizations\Factory\Entity\Hydrator;

use Core\Entity\Hydrator\Factory\ImageSetHydratorFactory;
use Organizations\Options\OrganizationLogoOptions;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class LogoHydratorFactory extends ImageSetHydratorFactory
{
    protected function getOptionsName()
    {
        return OrganizationLogoOptions::class;
    }
}
