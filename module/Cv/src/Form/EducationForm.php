<?php
namespace Cv\Form;

use Core\Form\SummaryForm;

class EducationForm extends SummaryForm
{
    protected $baseFieldset = 'EducationFieldset';

    public function init()
    {
        $this->setDescription(/*@translate*/ 'Focus on the work experience that gives added weight to your application. Add separate entries for each course. Start from the most recent.');
        $this->setIsDescriptionsEnabled(true);
        parent::init();
    }
}
