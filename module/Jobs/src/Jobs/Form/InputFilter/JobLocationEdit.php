<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Job.php */ 
namespace Jobs\Form\InputFilter;

use Zend\InputFilter\InputFilter;
//use Zend\InputFilter\Input;

class JobLocationEdit extends InputFilter
{
    
    public function init()
    {
//        $this->add(array(
//            'name' => 'applyId',
//            'required' => true,
//            'filters' => array(
//                array('name' => 'StringTrim')
//            ),
//        ));
//
//        $this->add(array(
//            'name' => 'company',
//            'required' => true,
//            'filters' => array(
//                array('name' => 'StringTrim')
//            ),
//        ));
        
        $this->add(array(
            'name' => 'title',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim')
            ),
        ));
        
//        $this->add(array(
//            'name' => 'description',
//            'required' => true,
//        ));
        
        $this->add(array(
            'name' => 'location',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim')
            ),
        ));
        
//        $this->add(array(
//            'name' => 'contactEmail',
//            'required' => true,
//            'filters' => array(
//                array('name' => 'StringTrim'),
//            ),
//            'validators' => array(
//                array('name' => 'EmailAddress'),
//            ),
//        ));
//
//        $this->add(array(
//            'name' => 'reference',
//            'filters' => array(
//                array('name' => 'StringTrim')
//            ),
//        ));
    }
}

