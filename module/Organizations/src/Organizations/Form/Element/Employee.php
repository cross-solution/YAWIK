<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Organizations\Form\Element;

use Auth\Entity\UserInterface;
use Core\Form\ViewPartialProviderInterface;
use Zend\Form\Element;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class Employee extends Element //implements ViewPartialProviderInterface
{

    protected $viewPartial = 'organizations/form/employee';

    public function setViewPartial($partial)
    {
        $this->viewPartial = $partial;

        return $this;
    }

    public function getViewPartial()
    {
        return $this->viewPartial;
    }

    public function getValue()
    {
        $value = parent::getValue();

        return $value instanceOf UserInterface ? $value->getId() : '__userId__';
    }

    public function getUser()
    {
        $value = parent::getValue();

        return $value instanceOf UserInterface ? $value : null;
    }

}