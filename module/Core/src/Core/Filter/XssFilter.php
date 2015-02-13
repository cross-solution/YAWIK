<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** XssFilter.php */
namespace Core\Filter;

use Zend\Filter\FilterInterface;
use Zend\Filter\Exception;

/**
 * Xss Filter
 *
 * @author Cristian Stinga <gelhausen@cross-solution.de>
 */
class XssFilter implements FilterInterface
{
    /**
     * @var HTMLPurifierFilter
     */
    protected $htmlPurifier;

    /**
     * @param HTMLPurifierFilter|\zf2htmlpurifier\Filter\HTMLPurifierFilter $purifier
     */
    public function __construct($purifier)
    {
        $this->setHtmlPurifier($purifier);
    }

    /**
     * @param HTMLPurifierFilter|\zf2htmlpurifier\Filter\HTMLPurifierFilter $purifier
     */
    public function setHtmlPurifier($purifier){
        $this->htmlPurifier = $purifier;
    }

    /**
     * @return HTMLPurifierFilter|\zf2htmlpurifier\Filter\HTMLPurifierFilter
     */
    public function getHtmlPurifier(){
        return $this->htmlPurifier;
    }

    /**
     * Returns the result of filtering $value
     *
     * @param mixed $value
     * @throws Exception\RuntimeException If filtering $value is impossible
     * @return mixed
     */
    public function filter($value)
    {
         $htmlPurifier = $this->getHtmlPurifier();
         return $htmlPurifier->filter($value);
    }
}