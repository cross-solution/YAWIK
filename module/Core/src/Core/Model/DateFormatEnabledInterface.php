<?php

namespace Core\Model;

interface DateFormatEnabledInterface
{
    
    public function getFormattedDate($property, $format="%x");
}