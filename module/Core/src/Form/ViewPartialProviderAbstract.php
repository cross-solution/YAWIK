<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Core forms */
namespace Core\Form;

use Zend\Form\ElementInterface;

/**
 * Enables form elements to provide a view partial when being rendered.
 *
 * @author Mathias Weitz <weitz@cross-solution.de>
 */
abstract class ViewPartialProviderAbstract extends Container implements ViewPartialProviderInterface
{
    
    /**
     * View partial name.
     * @var string
     */
    protected $partial = 'core/form/container-view';
    
    /**
     * Sets the view partial name.
     *
     * @param String $partial
     * @return ElementInterface fluent interface
     */
    public function setViewPartial($partial)
    {
        $this->partial = $partial;
        return $this;
    }
    
    /**
     * Gets the view partial name.
     *
     * @return string
     */
    public function getViewPartial()
    {
        return $this->partial;
    }
}
