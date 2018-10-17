<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/**  */
namespace Applications\Form\Element;

use Zend\Form\Element\Button;

/**
 * Class Ref
 *
 * @package Applications\Form\Element
 */
class Ref extends Button
{
    /**
     * @var string
     */
    protected $helper = 'formInfoCheckBox';
}
