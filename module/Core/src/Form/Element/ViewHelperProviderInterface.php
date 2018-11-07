<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** */
namespace Core\Form\Element;

/**
 * Enables formular elements to provide a view helper.
 *
 * This helper will then be used by the formular view helpers
 * {@link \Core\Form\View\Helper\Form},
 * {@link \Core\Form\View\Helper\FormCollection},
 * {@link \Core\Form\View\Helper\SummaryForm},
 * etc.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
interface ViewHelperProviderInterface
{
    /**
     * Sets the view helper instance or service name.
     *
     * @param string $helper
     */
    public function setViewHelper($helper);

    /**
     * Gets the view helper instance or service name.
     *
     * @return \Zend\View\Helper\HelperInterface|string
     */
    public function getViewHelper();
}
