<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Service;

use Zend\Form\ElementInterface;

/**
 * this is for form-Elements, which want to know more about embedding form
 * Interface OptionValueInterface
 * @package Core\Service
 */
interface OptionValueInterface
{
    public function init(ElementInterface $element);
}
