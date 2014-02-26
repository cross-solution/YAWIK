<?php

namespace Jobs\Form;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\Form\Form;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Entity\Hydrator\Strategy\ArrayToCollectionStrategy;

class Import extends Form
{
    
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }
    
     public function setData($data)
    {
        if ($data instanceof Traversable) {
            $data = ArrayUtils::iteratorToArray($data);
        }
        if (!array_key_exists('job',$data)) {
            $data = array('job' => $data);
        }
        
        return parent::setData($data);
    }
    
    
    
    public function init()
    {
        $this->setName('job-create');
        $this->setAttribute('id', 'job-create');
 
        
        $this->add(array(
            'type' => 'Jobs/ImportFieldset',
            'name' => 'job',
            'options' => array(
                'use_as_base_fieldset' => true
            ),
        ));       
        
        $this->add(array(
            'type' => 'DefaultButtonsFieldset'
        ));

    }
}