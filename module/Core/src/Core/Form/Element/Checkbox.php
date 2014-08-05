<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/**  */ 
namespace Core\Form\Element;

use Zend\Form\Element\Checkbox as ZfCheckbox;
/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Checkbox extends ZfCheckbox implements ViewHelperProviderInterface
{
    protected $helper = 'formcheckbox';
    
    public function setViewHelper($helper)
    {
        $this->helper = $helper;
        return $this;
    }
    
    public function getViewHelper()
    {
        return $this->helper;
    }
}
