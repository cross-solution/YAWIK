<?php

namespace Cv\Form;

use Core\Form\SummaryForm;

class LanguageSkillForm extends SummaryForm
{
    protected $baseFieldset = 'Cv/LanguageSkillFieldset';

    public function init()
    {
        $this->setDescription(/*@translate*/ 'Please select a language and self-assess your level');
        $this->setIsDescriptionsEnabled(true);
        parent::init();
    }
}
