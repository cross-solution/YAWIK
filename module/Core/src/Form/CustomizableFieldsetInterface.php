<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
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