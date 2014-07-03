<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/**  */ 
namespace Core\Form;

/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class SummaryForm extends Form implements SummaryFormInterface
{
    
    protected $renderMode = 'all';
    protected $displayMode = 'form';
    
    public function setRenderMode($mode)
    {
        $this->renderMode = $mode;
        return $this;
    }
    
    public function getRenderMode()
    {
        return $this->renderMode;
    }
    
    public function setDisplayMode($mode)
    {
        $this->displayMode = $mode;
        return $this;
    }
    
    public function getDisplayMode()
    {
        return $this->displayMode;
    }
    
    protected function addButtonsFieldset()
    {
        $this->add(array(
            'type' => 'SummaryFormButtonsFieldset'
        ));
    }
}
