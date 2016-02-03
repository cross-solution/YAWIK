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

use Zend\Form\FormInterface;

/**
 * SummaryForm interface.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
interface SummaryFormInterface extends FormInterface
{
    /**#@+
     * Render mode constants.
     * @var string
     */
    const RENDER_ALL     = 'all';
    const RENDER_FORM    = 'form';
    const RENDER_SUMMARY = 'summary';
    /**#@-*/
    
    /**#@+
     * Display mode constants.
     * @var string
     */
    const DISPLAY_FORM    = 'form';
    const DISPLAY_SUMMARY = 'summary';
    /**#@-*/
    
    /**
     * Gets the mode the form should be rendered in.
     *
     * @return string
     */
    public function getRenderMode();
    
    /**
     * Sets the mode the form should be rendered in.
     *
     * @param string $mode
     * @return self
     */
    public function setRenderMode($mode);
    
    /**
     * Gets the mode the form should be initially displayed.
     *
     * @return string
     */
    public function getDisplayMode();
    
    /**
     * Sets the mode the form should be initially displayed.
     *
     * @param string $mode
     * @return self
     */
    public function setDisplayMode($mode);
}
