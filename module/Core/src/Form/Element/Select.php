<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/**  */
namespace Core\Form\Element;

use Zend\Form\Element\Select as ZfSelect;

/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Select extends ZfSelect implements ViewHelperProviderInterface
{
    /**
     * @var string
     */
    protected $helper = 'formSelect';

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
