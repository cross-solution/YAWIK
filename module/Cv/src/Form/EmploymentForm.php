<?php

namespace Cv\Form;

use Core\Form\SummaryForm;

class EmploymentForm extends SummaryForm
{
    protected $baseFieldset = 'EmploymentFieldset';

    public function init()
    {
        $this->setDescription(/*@translate*/' Focus on the work experience that gives added weight to your application.<br>Add separate entries for each experience. Start with the most recent.<br>If your work experience is limited:<ul><li>describe your education and training first</li><li>mention volunteering or (paid/unpaid) work placements which provide evidence of work experience.</li></ul>');
        $this->setIsDescriptionsEnabled(true);
        parent::init();
    }
}
