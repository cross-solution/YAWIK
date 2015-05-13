<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Filter;

use Zend\Filter\FilterInterface;
use Zend\Filter\Exception;

class StripTags implements FilterInterface
{
    public function filter($value)
    {
        return strip_tags($value);
    }
}