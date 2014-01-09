<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

namespace Core\Html2Pdf;
use Zend\EventManager\EventManagerInterface;

interface PdfInterface {
    public function attach(EventManagerInterface $events);
}