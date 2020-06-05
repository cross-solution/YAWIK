<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license       MIT
 */

namespace Jobs\Form\InputFilter;

use Laminas\InputFilter\InputFilter;

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
