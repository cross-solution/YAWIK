<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Jobs\Form\InputFilter;

use Zend\InputFilter\InputFilter;

class CompanyName extends InputFilter
{
    public function init()
    {
        $this->add(array(
            'name' => 'company',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags')
            ),
        ));
    }
}

