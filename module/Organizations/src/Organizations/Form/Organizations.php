<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Organizations forms */
namespace Organizations\Form;

use Core\Form\SummaryForm;

/**
 * Organization forms container 
 *
 * @author Mathias Weitz <weitz@cross-solution.de>
 */

class Organizations extends SummaryForm {
    
    protected $baseFieldset = 'Organizations/OrganizationFieldset';
    protected $displayMode = self::DISPLAY_SUMMARY;
    
}