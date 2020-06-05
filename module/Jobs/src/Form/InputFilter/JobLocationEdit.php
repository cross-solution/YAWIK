<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 */

/** Job.php */
namespace Jobs\Form\InputFilter;

use Laminas\InputFilter\InputFilter;

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
