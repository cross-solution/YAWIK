<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
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