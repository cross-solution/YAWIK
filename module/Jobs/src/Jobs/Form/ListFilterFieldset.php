<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ListFilterFieldset.php */ 
namespace Jobs\Form;

use Jobs\Entity\Status;
use Zend\Form\Fieldset;
use Zend\Form\FormInterface;

/**
 * Defines the formular fields of the job opening search formular
 *
 * @package Jobs\Form
 */
class ListFilterFieldset extends Fieldset
{
    /**
     * Show my jobs, all jobs and job status filter
     *
     * @var bool
     */
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
                        'all' => /*@translate*/ 'All',
                        Status::ACTIVE => /*@translate*/ 'Active',
                        Status::INACTIVE => /*@translate*/ 'Inactive',
                    )
                ),
                'attributes' => array(
                    'value' => 'all',
                )
            ));
        }
        $this->add(array(
            'name' => 'search',
            'options' => array(
                'label' => /*@translate*/ 'Job title',
            ),
        ));

        $this->add(array(
                       'name' => 'l',
                       'type' => 'Location',
                       'options' => array(
                           'label' => /*@translate*/ 'Location',
                       ),
                   ));

        $this->add(array(
                       'name' => 'd',
                       'type' => 'Zend\Form\Element\Select',
                       'options' => array(
                           'label' => /*@translate*/ 'Distance',
                           'value_options' => array(
                               '5' => '5 km',
                               '10'  => '10 km',
                               '20' => '20 km',
                               '50' => '50 km',
                               '100' => '100 km'
                           ),
                       ),
                   ));
    }

    /**
     * @param FormInterface $form
     */
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
