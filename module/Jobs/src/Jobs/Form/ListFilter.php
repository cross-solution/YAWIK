<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ListFilter.php */ 
namespace Jobs\Form;

use Core\Form\Form;
use Core\Form\ViewPartialProviderInterface;

/**
 * Defines the job opening search formular
 *
 * @package Jobs\Form
 */
class ListFilter extends Form implements ViewPartialProviderInterface
{
    /**
     * @var string $viewPartial view script for the search formular
     */
    protected $viewPartial = 'jobs/form/list-filter';

    /**
     * @var bool $isExtended if set, acl is used
     */
    protected $isExtended;
    
    public function __construct($useAcl = false)
    {
        $this->isExtended = (bool) $useAcl;
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
        $this->setAttribute('data-handle-by', 'native');
        
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

