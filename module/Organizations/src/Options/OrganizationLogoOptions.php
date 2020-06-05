<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Organizations\Options;

use Core\Options\ImageSetOptions;
use Organizations\Entity\OrganizationImage;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class OrganizationLogoOptions extends ImageSetOptions
{
    protected $entityClass = OrganizationImage::class;

}