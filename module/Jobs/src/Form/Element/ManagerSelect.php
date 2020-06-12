<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Jobs\Form\Element;

use Core\Form\Element\Select;
use Laminas\Form\Element;
use Laminas\Stdlib\ArrayUtils;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class ManagerSelect extends Select
{
    public function init()
    {
        $this->setDisableInArrayValidator(true);
    }

    public function setValue($value)
    {
        if ('__empty__' == $value) {
            return $this->setValue([]);
        }

        $this->setAttribute('data-initialValue', join(',', $value));

        return parent::setValue($value);
    }
}
