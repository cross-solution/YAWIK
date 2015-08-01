<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/**  */
namespace Applications\Form;

use Core\Form\DisableElementsCapableInterface;
use Zend\Form\Fieldset;
use Core\Form\EmptySummaryAwareInterface;

/**
 * Facts fieldset.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class FactsFieldset extends Fieldset implements DisableElementsCapableInterface, EmptySummaryAwareInterface
{
    /**
     * The empty summary notice.
     *
     * @var string
     */
    protected $emptySummaryNotice = /*@translate*/ 'Click here to enter facts.';
    
    public function init()
    {
        $this->setHydrator(new \Core\Entity\Hydrator\EntityHydrator())
             ->setName('base');

        $a=$this->add(array(
            'name' => 'willingnessToTravel',
            'type' => '\Zend\Form\Element\Select',
            'options' => array(
                'value_options' => array(
                    '' => '', // needed for jquery select2 to render the placeholder
                    "yes"=>/*@translate*/ "Yes",
                    "conditioned" => /*@translate*/ "conditioned",
                    "no"=>/*@translate*/ "No"),
                'label' => /*@translate*/ 'Willingness to travel',
                'description' => /*@translate*/ 'Enter your willingness to travel.',
                'disable_capable' => array(
                    'description' => /*@translate*/ 'Ask the applicant about the willingness to travel',
                ),
            ),
            'attributes' => array(
                'data-placeholder' => /*@translate*/ 'please select',
                'data-allowclear' => 'true',
            ),
        ));

        $this->add(array(
            'name' => 'earliestStartingDate',
            'type' => "text",
            'options' => array(
                'label' => /*@translate*/ 'Earliest starting date',
                'description' => /*@translate*/ 'Enter the earliest starting date.',
                'disable_capable' => array(
                    'description' => /*@translate*/ 'Ask the applicant about the earliest starting date.',
                ),
            ),
        ));

        $this->add(array(
            'name' => 'expectedSalary',
            'options' => array(
                'label' => /*@translate*/ 'Expected salary',
                'description' => /*@translate*/ 'Your salary requirements should be the annual amount before taxes. Do not forget to provide the currency sign.',
                'disable_capable' => array(
                    'description' => /*@translate*/ 'Ask users about their expected salary.',
                ),
            ),
        ));

        $this->add(array(
                       'name' => 'drivingLicense',
                       'type' => '\Zend\Form\Element\Select',
                       'options' => array(
                           'value_options' => array(
                               '' => '', // needed for jquery select2 to render the placeholder
                               "1"=>/*@translate*/ "Yes",
                               "0"=>/*@translate*/ "No"),
                           'label' => /*@translate*/ 'driving license',
                           'description' => /*@translate*/ 'Do you have a driving license?',
                           'disable_capable' => array(
                               'description' => /*@translate*/ 'Ask the applicant, if he has a driving license.',
                           ),
                       ),
                   ));
    }
    
    public function isSummaryEmpty()
    {
        foreach ($this as $element) { /* @var $element \Zend\Form\ElementInterface */
            if ('' != $element->getValue()) {
                return false;
            }
        }

        return true;
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

    public function setIsDisableCapable($flag)
    {
        $this->options['is_disable_capable'] = $flag;

        return $this;
    }

    public function isDisableCapable()
    {
        return isset($this->options['is_disable_capable'])
               ? $this->options['is_disable_capable'] : true;
    }

    public function setIsDisableElementsCapable($flag)
    {
        $this->options['is_disable_elements_capable'] = $flag;

        return $this;
    }

    public function isDisableElementsCapable()
    {
        return isset($this->options['is_disable_elements_capable'])
            ? $this->options['is_disable_elements_capable']
            : true;
    }

    public function disableElements(array $map)
    {
        if (in_array('expectedSalary', $map)) {
            $this->remove('expectedSalary');
        }
        if (in_array('willingnessToTravel', $map)) {
            $this->remove('willingnessToTravel');
        }
        if (in_array('earliestStartingDate', $map)) {
            $this->remove('earliestStartingDate');
        }
        if (in_array('drivingLicense', $map)) {
            $this->remove('drivingLicense');
        }
        return $this;
    }
}

