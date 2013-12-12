<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** ListFilter.php */ 
namespace Jobs\Form;

use Core\Form\Form;
use Core\Form\ViewPartialProviderInterface;

class ListFilter extends Form implements ViewPartialProviderInterface
{
    
    protected $viewPartial = 'jobs/form/list-filter';
    protected $isExtended;
    
    public function __construct($extended = false)
    {
        $this->isExtended = (bool) $extended;
        parent::__construct();
    }
    
    public function setViewPartial($partial)
    {
        $this->viewPartial = $partial;
        return $this;
    }
    
    public function getViewPartial()
    {
        return $this->viewPartial;
    }
    
    public function init()
    {
        $this->setName('jobs-list-filter');
        $this->setAttribute('id', 'jobs-list-filter');
        
        $this->add(array(
            'type' => 'Jobs/ListFilterFieldset' . ($this->isExtended ? 'Extended' : ''), 
            'options' => array(
                'use_as_base_fieldset' => true
            ),
        ));
        
        $this->add(array(
            'type' => 'Core/ListFilterButtons'
        ));
    }
}

