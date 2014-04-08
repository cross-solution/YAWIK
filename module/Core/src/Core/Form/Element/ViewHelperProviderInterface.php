<?php

namespace Core\Form\Element;

interface ViewhelperProviderInterface 
{
    public function getViewhelper();
    public function allowErrorMessages();
}
