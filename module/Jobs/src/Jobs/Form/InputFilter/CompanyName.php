<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Jobs\Form\InputFilter;

use Zend\InputFilter\InputFilter;

class CompanyName extends InputFilter
{
    public function init()
    {
        $this->add(
            [
                'name'     => 'companyId',
                'required' => true,
            ]
        );

        $this->add([
                'name' => 'managers',
                'required' => false,
            ]);
    }
}
