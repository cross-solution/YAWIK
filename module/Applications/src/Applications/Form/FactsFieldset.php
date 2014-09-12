<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
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
    }
    
    public function isSummaryEmpty()
    {
        // expectedSalary might be disabled, so we need to check that first.
        return !$this->has('expectedSalary') || '' == $this->get('expectedSalary')->getValue();
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

        return $this;
    }
}

