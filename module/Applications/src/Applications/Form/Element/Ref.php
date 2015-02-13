<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/**  */ 
namespace Applications\Form\Element;
use Zend\Form\Element\Button as Refref;
use Core\Form\Element\ViewHelperProviderInterface;

/**
 *
 *
 */
class Ref extends Refref 
//implements ViewHelperProviderInterface
{
    protected $helper = 'forminfocheckbox';
   
    //public function getViewHelper() {
    //    $this->helper;
    //}
    
    //public function setViewHelper($helper) {
    //    return $this;
    //}
    
}