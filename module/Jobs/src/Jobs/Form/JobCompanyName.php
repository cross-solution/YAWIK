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

use Core\Form\Form;
use Core\Form\SummaryForm;
use Core\Entity\Hydrator\EntityHydrator;


class JobCompanyName extends SummaryForm
{
    protected $baseFieldset = 'Jobs/CompanyNameFieldset';
    protected $label = /*@translate*/ 'Companyname';

    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }

    /*
    public function init()
    {
        $this->setName('jobs-form-companyname');
        $this->setAttributes(array(
            'id' => 'jobs-form-companyname',
            //'data-handle-by' => 'native'
        ));


        $this->add(array(
            'type' => 'Jobs/CompanyNameFieldset',
            'name' => 'jobCompanyName',
            'options' => array(
                'use_as_base_fieldset' => true,
            ),
        ));
    }
    */
}