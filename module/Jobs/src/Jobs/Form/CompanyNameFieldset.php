<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form;

use Zend\Form\Fieldset;

/**
 * Defines the formular fields used in the formular for entering the hiring organization name
 *
 * @package Jobs\Form
 */
class CompanyNameFieldset extends Fieldset
{
    public function init()
    {
        $this->setAttribute('id', 'jobcompanyname-fieldset');
        $this->setName('jobCompanyName');

        $this->add(
            array(
                'type' => 'Jobs/CompanyNameElement',
                'property' => true,
                'name' => 'company',
                'options' => array(
                    'label' => /*@translate*/ 'Companyname',
                    //'description' => /*@translate*/ 'The name of the hiring organization will be shown in search results.'
                ),
            )
        );

        $this->add(
            array(
                'type' => 'Hidden',
                'name' => 'companyId'
            )
        );
    }
}