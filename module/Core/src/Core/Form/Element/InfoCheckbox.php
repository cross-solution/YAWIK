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

/**
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class InfoCheckbox extends Checkbox implements ViewHelperProviderInterface
{
    protected $helper = 'formInfoCheckBox';
}