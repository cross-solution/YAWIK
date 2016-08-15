<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */
namespace Core\Form;

/**
 * @property string $defaultEmptySummaryNotice
 * @author fedys
 * @since 0.26
 */
trait EmptySummaryAwareTrait
{

    /**
     * The empty summary notice.
     *
     * @var string
     */
    protected $emptySummaryNotice;

    /**
     * @see \Core\Form\EmptySummaryAwareInterface::isSummaryEmpty()
     */
    public function isSummaryEmpty()
    {
        foreach ($this as $element) { /* @var $element \Zend\Form\ElementInterface */
            if ($element->getValue()) {
                return false;
            }
        }
        return true;
    }

    /**
     * @see \Core\Form\EmptySummaryAwareInterface::setEmptySummaryNotice()
     */
    public function setEmptySummaryNotice($message)
    {
        $this->emptySummaryNotice = $message;
        return $this;
    }

    /**
     * @see \Core\Form\EmptySummaryAwareInterface::getEmptySummaryNotice()
     */
    public function getEmptySummaryNotice()
    {
        if (! isset($this->emptySummaryNotice) && property_exists($this, 'defaultEmptySummaryNotice')) {
            $this->emptySummaryNotice = $this->defaultEmptySummaryNotice;
        }
        
        return $this->emptySummaryNotice;
    }
}
