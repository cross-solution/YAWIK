<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

/** Core forms */
namespace Core\Form;

interface FormParentInterface
{
    public function setParent($parent);

    public function getParent();

    public function hasParent();
}
