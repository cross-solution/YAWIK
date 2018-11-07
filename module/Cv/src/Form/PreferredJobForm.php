<?php

namespace Cv\Form;

use Core\Form\SummaryForm;

class PreferredJobForm extends SummaryForm
{
    protected $baseFieldset = PreferredJobFieldset::class;
    protected $displayMode = self::DISPLAY_SUMMARY;

    public function init()
    {
        //  $this->setDescription(/*@translate*/' Where do you want to work tomorrow. This heading gives an immediate overview of your desired next job.');
        $this->setIsDescriptionsEnabled(true);
        parent::init();
    }
}
