<?php

namespace Jobs\Form;

use Core\Form\SummaryForm;
use Core\Entity\Hydrator\EntityHydrator;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Defines the base formular of a job opening.
 *
 * @package Jobs\Form
 */
class JobTitleLocation extends SummaryForm implements InputFilterProviderInterface
{

    protected $baseFieldset = 'Jobs/JobFieldset';
    
    /**
     * label of the Title and Location Form.
     */
    protected $label = /*@translate*/ 'Title and job location';
    
    public function getInputFilterSpecification()
    {
        $formName = $this->getFormName();
        return array(
            $formName => array('type' => 'new' == $this->getOption('mode') ? 'Jobs/Location/New' : 'Jobs/Location/Edit')
        );
    }
}