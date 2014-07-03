<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/**  */ 
namespace Core\Form;

use Zend\Form\FormInterface;
/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
interface SummaryFormInterface extends FormInterface
{
    const RENDER_ALL = 'all';
    const RENDER_FORM = 'form';
    const RENDER_SUMMARY = 'summary';
    
    const DISPLAY_FORM = 'form';
    const DISPLAY_SUMMARY = 'summary';
    
    public function getRenderMode();
    public function setRenderMode($mode);
    
    public function getDisplayMode();
    public function setDisplayMode($mode);
}