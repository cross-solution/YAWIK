<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** ListFilterFieldset.php */ 
namespace Jobs\Form;

use Zend\Form\Fieldset;
use Zend\Form\FormInterface;

class ListFilterFieldset extends Fieldset
{
    protected $isExtended;
    
    public function __construct($extended = false)
    {
        $this->isExtended = (bool) $extended;
        parent::__construct();
    }
    
    public function init()
    {
        $this->setName('params');
        
        $this->add(array(
            'type' => 'Hidden',
            'name' => 'page',
            'attributes' => array(
                'value' => 1,
            )
        ));
        
        if ($this->isExtended) {
            $this->add(array(
                'type' => 'Radio',
                'name' => 'by',
                'options' => array(
                    'value_options' => array(
                        'all' => /*@translate*/ 'Show all jobs',
                        'me'  => /*@translate*/ 'Show my jobs',
                    ),
                ),
                'attributes' => array(
                    'value' => 'all',
                )
                
            ));
            
            $this->add(array(
                'type' => 'Radio',
                'name' => 'status',
                'options' => array(
                    'value_options' => array(
                        'active' => /*@translate*/ 'Active',
                        'inactive' => /*@translate*/ 'Inactive',
                    )
                ),
                'attributes' => array(
                    'value' => 'active',
                )
            ));
        }
        $this->add(array(
            'name' => 'search',
            'options' => array(
                'label' => /*@translate*/ 'Job title',
            ),
        ));
    }
    
    public function prepareElement(FormInterface $form)
    {
        foreach ($this->byName as $elementOrFieldset) {
            // Recursively prepare elements
            if ($elementOrFieldset instanceof ElementPrepareAwareInterface) {
                $elementOrFieldset->prepareElement($form);
            }
        }
    }
}

