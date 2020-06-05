<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 */

/**  */
namespace Applications\Form\Element;

use Laminas\Form\Element\Button;

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
