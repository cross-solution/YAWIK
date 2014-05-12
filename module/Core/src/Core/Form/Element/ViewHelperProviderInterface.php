<?php

namespace Core\Form\Element;

interface ViewHelperProviderInterface 
{
    public function getViewHelper();
    public function setViewHelper($helper);
}
