<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\Html2Pdf;

use Zend\EventManager\EventManagerInterface;

interface PdfInterface
{
    public function attach(EventManagerInterface $events);
    
    public function attachMvc(EventManagerInterface $events);
}
