<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Form\Element;

use Core\Form\Element\Select;
use Zend\Form\Element;
use Zend\Stdlib\ArrayUtils;

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
