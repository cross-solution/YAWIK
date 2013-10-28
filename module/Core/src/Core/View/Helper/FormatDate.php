<?php

namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;


/**
 * @todo Write factory, configuration must be possible
 * @author mathias
 *
 */


class FormatDate extends AbstractHelper
{
    
    protected $format;
    
    public function setFormat($format)
    {
        $this->format = (string) $format;
        return $this;
    }
    
    public function getFormat()
    {
        if (null === $this->format) {
            $this->setFormat('%x %X');
        }
        return $this->format;
    }
    
    public function __invoke(\DateTime $date, $format=null)
    {
        if (null === $format) {
            $format = $this->getFormat();
        }
        return strftime($format, $date->getTimestamp());
    }
}