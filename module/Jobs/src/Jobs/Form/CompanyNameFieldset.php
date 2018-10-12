<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form;

use Core\Form\HeadscriptProviderInterface;
use Core\Form\ViewPartialProviderInterface;
use Core\Form\ViewPartialProviderTrait;
use Zend\Form\Fieldset;

/**
 * Defines the formular fields used in the formular for entering the hiring organization name
 *
 * @package Jobs\Form
 */
class CompanyNameFieldset extends Fieldset implements HeadscriptProviderInterface, ViewPartialProviderInterface
{
    use ViewPartialProviderTrait;

    private $defaultPartial = 'jobs/form/company-name-fieldset';

    /**
     *
     *
     * @var array
     */
    protected $headscripts = [ 'Jobs/js/forms.manager-select.js' ];

    public function setHeadscripts(array $scripts)
    {
        $this->headscripts = $scripts;

        return $this;
    }

    public function getHeadscripts()
    {
        return $this->headscripts;
    }


    public function init()
    {
        $this->setAttribute('id', 'jobcompanyname-fieldset');
        $this->setName('jobCompanyName');

        $this->add(
            [
                'type' => 'Jobs/HiringOrganizationSelect',
                'property' => true,
                'name' => 'companyId',
                'options' => [
                    'label' => /*@translate*/ 'Companyname',
                ],
                'attributes' => [
                    'data-placeholder' => /*@translate*/ 'Select hiring organization',
                    'data-allowclear'  => 'false',
                    'data-width' => '100%'
                ],
            ]
        );

        $this->add([
                'type' => 'Jobs/ManagerSelect',
                'property' => true,
                'name' => 'managers',
                'options' => [
                    'description' => /*@translate*/ 'There are department managers assigned to your organization. Please select the department manager, who will receive notifications for incoming applications',
                    'label' => /*@translate*/ 'Choose Managers',
                ],
                'attributes' => [
                    'data-allowclear'  => true,
                    'data-width' => '100%',
                    'multiple' => true,
                    'class' => 'manager-select',
                    'data-organization-element' => 'organization-select',
                ],

            ]);
    }


}
