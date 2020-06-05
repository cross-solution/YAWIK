<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Jobs\Factory\Form;

use Core\Factory\Form\AbstractCustomizableFieldsetFactory;
use Jobs\Form\BaseFieldset;

/**
 * Factory for the BaseFieldset (Job Title and Location)
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 */
class BaseFieldsetFactory extends AbstractCustomizableFieldsetFactory
{
    const OPTIONS_NAME = 'Jobs/BaseFieldsetOptions';

    const CLASS_NAME = BaseFieldset::class;
}
