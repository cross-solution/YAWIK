<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Rating.php */
namespace Core\Form\Element;

use Zend\Form\Element;
use Zend\Form\Element\Date;
use Core\Entity\RatingInterface;

/**
 * Star rating element.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class DatePicker extends Date implements ViewHelperProviderInterface
{

    /**
     * @var string
     */
    protected $helper = 'formDatePicker';

    /**
     * @param string|\Zend\View\Helper\HelperInterface $helper
     * @return $this|ViewHelperProviderInterface
     */
    public function setViewHelper($helper)
    {
        $this->helper = $helper;
        return $this;
    }

    /**
     * @return string|\Zend\View\Helper\HelperInterface
     */
    public function getViewHelper()
    {
        return $this->helper;
    }
}
