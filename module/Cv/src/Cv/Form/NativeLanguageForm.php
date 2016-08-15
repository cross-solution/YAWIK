<?php

namespace Cv\Form;

use Core\Form\SummaryForm;

class NativeLanguageForm extends SummaryForm
{
    protected $baseFieldset = 'Cv/NativeLanguageFieldset';
    protected $displayMode = self::DISPLAY_SUMMARY;
}
