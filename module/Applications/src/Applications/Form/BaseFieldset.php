<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** AttachmentsFieldset.php */ 
namespace Applications\Form;

use Zend\Form\Fieldset;
use Core\Form\EmptySummaryAwareInterface;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Fieldset for base informations of an application.
 * 
 * Currently, this is only the freetext summary.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class BaseFieldset extends Fieldset implements EmptySummaryAwareInterface,
                                               InputFilterProviderInterface
{
    
    protected $emptySummaryNotice = /*@translate*/ 'Click here to enter a summary.';
    
    /**
     * {@inheritDoc}
     * @see \Zend\Form\Element::init()
     */
    public function init()
    {
        $this->setName('base')
             //->setLabel('Summary')
             ->setHydrator(new \Core\Entity\Hydrator\EntityHydrator());
             
                     
        $this->add(array(
            'type' => 'textarea',
            'name' => 'summary',
            'options' => array(
                'description' => /*@translate*/ '<strong>Please note</strong>: HTML tags get stripped out. Line breaks are preserved.'
                //'label' => /*@translate*/ 'Summary'
            ),
        ));
    }
    
    public function isSummaryEmpty()
    {
        return '' == $this->get('summary')->getValue();
    }
    
    public function setEmptySummaryNotice($message)
    {
        $this->emptySummaryNotice = $message;
        return $this;
    }
    
    public function getEmptySummaryNotice()
    {
        return $this->emptySummaryNotice;
    }
    
    public function getInputFilterSpecification()
    {
        return array(
            'summary' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
            ),
        );
    }
}

