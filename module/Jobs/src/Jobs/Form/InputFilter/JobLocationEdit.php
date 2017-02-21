<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Job.php */
namespace Jobs\Form\InputFilter;

use Zend\InputFilter\InputFilter;

class JobLocationEdit extends InputFilter
{
    public function init()
    {
        $this->add(
            [
                'name'     => 'title',
                'required' => true,
                'filters'  => [
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                ],
            ]
        );

        $this->add(
            [
                'name'     => 'geoLocation',
                'required' => true,
                'filters'  => [
                    array('name' => 'StringTrim')
                ],
            ]
        );
    }
}
