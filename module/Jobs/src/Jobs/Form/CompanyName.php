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

use Core\Form\SummaryForm;
use Core\Entity\Hydrator\EntityHydrator;
use Zend\InputFilter\InputFilterProviderInterface;


/**
 * Defines the form for entering the hiring organization name
 *
 * @package Jobs\Form
 */
class CompanyName extends SummaryForm implements InputFilterProviderInterface
{
    /**
     * formular fields are defined in JobsCompanyNameFieldset
     *
     * @var string
     */
    protected $baseFieldset = 'Jobs/CompanyNameFieldset';

    /**
     * header of the formular box
     *
     * @var string
     */
    protected $label = /*@translate*/ 'Companyname';

    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }

    public function getInputFilterSpecification()
    {
        $formName = $this->getFormName();

        return array(
            $formName => array(
                'type' => 'Jobs/Company'
            )
        );
    }
}