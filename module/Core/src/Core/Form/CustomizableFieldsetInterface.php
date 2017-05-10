<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Form;

use Core\Options\FieldsetCustomizationOptions;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
interface CustomizableFieldsetInterface
{
    public function setCustomizationOptions(FieldsetCustomizationOptions $options);
    public function getCustomizationOptions();
}