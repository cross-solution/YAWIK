<?php

namespace Jobs\Form;

use Core\Form\SummaryForm;
use Core\Entity\Hydrator\EntityHydrator;
use Zend\InputFilter\InputFilterProviderInterface;

class JobTitleLocation extends SummaryForm implements InputFilterProviderInterface
{

    protected $baseFieldset = 'Jobs/JobFieldset';

    protected $label = /*@translate*/ 'Job details';
    
    public function getInputFilterSpecification()
    {
        $formName = $this->getFormName();
        return array(
            $formName => array('type' => 'new' == $this->getOption('mode') ? 'Jobs/Location/New' : 'Jobs/Location/Edit')
        );
    }
}