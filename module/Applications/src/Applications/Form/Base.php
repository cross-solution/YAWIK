<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/**  */ 
namespace Applications\Form;

use Core\Form\SummaryForm;
/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Base extends SummaryForm
{
    protected $wrapElements = false;
    protected $baseFieldset = 'Applications/BaseFieldset';
    protected $displayMode = 'summary';
    
    
    public function init()
    {
        $this->setLabel(/*@translate*/ 'Summary');
        parent::init();
    }
}
