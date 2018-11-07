<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** AttachmentsFieldset.php */
namespace Applications\Form;

use Core\Form\DisableElementsCapableInterface;
use Zend\Form\Fieldset;
use Core\Form\EmptySummaryAwareInterface;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Fieldset for base information of an application.
 *
 * Currently, this is only the freetext summary.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class BaseFieldset extends Fieldset implements
    DisableElementsCapableInterface,
    EmptySummaryAwareInterface,
    InputFilterProviderInterface
{
    /**
     * The empty summary notice.
     *
     * @var string
     */
    protected $emptySummaryNotice = /*@translate*/ 'Click here to enter a summary.';

    /**
     * initialize base fieldset
     */
    public function init()
    {
        $this->setHydrator(new \Core\Entity\Hydrator\EntityHydrator())
             ->setName('base');
             
        $this->add(
            array(
            'type' => 'textarea',
            'name' => 'summary',
            'options' => array(
                'description' => /*@translate*/ '<strong>Please note</strong>: HTML tags get stripped out. Line breaks are preserved.',
                'is_disable_capable' => false,
            ),
            )
        );
    }

    /**
     * returns true, if all form fields of the fieldset are empty.
     *
     * @return bool
     */
    public function isSummaryEmpty()
    {
        return '' == $this->get('summary')->getValue();
    }

    /**
     * Sets the empty summary notice, which can be shown, if the summary is empty.
     *
     * @param $message
     *
     * @return $this
     */
    public function setEmptySummaryNotice($message)
    {
        $this->emptySummaryNotice = $message;
        return $this;
    }

    /**
     * Gets the empty summary notice
     *
     * @return string
     */
    public function getEmptySummaryNotice()
    {
        return $this->emptySummaryNotice;
    }

    /**
     * Gets the input filter specification
     *
     * @return array
     */
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

    /**
     * @param bool $flag
     *
     * @return $this
     */
    public function setIsDisableCapable($flag)
    {
        $this->options['is_disable_capable'] = $flag;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDisableCapable()
    {
        return isset($this->options['is_disable_capable'])
               ? $this->options['is_disable_capable'] : true;
    }

    /**
     * @param bool $flag
     *
     * @return $this
     */
    public function setIsDisableElementsCapable($flag)
    {
        $this->options['is_disable_elements_capable'] = $flag;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDisableElementsCapable()
    {
        return isset($this->options['is_disable_elements_capable'])
            ? $this->options['is_disable_elements_capable']
            : true;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Form\DisableElementsCapableInterface::disableElements()
     */
    public function disableElements(array $map)
    {
        if (in_array('summary', $map)) {
            $this->remove('summary');
        }

        return $this;
    }
}
