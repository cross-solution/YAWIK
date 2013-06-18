<?php

namespace Core\Form;

interface ViewPartialProviderInterface
{
    public function setViewPartial($partial);
    public function getViewPartial();
}