<?php

namespace Core\Entity;

interface DateFormatEnabledInterface
{
    
    public function getFormattedDate($property, $format="%x");
}