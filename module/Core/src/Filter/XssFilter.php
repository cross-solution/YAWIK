<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** XssFilter.php */
namespace Core\Filter;

use Zend\Filter\FilterInterface;
use Zend\Filter\Exception;
use HTMLPurifier;


/**
 * Xss Filter
 *
 * @author Cristian Stinga <gelhausen@cross-solution.de>
 */
class XssFilter implements FilterInterface
{
    /**
     * @var HTMLPurifier
     */
    protected $htmlPurifier;

    /**
     * Construct the html purifier filter
     *
     * @param HTMLPurifier $purifier
     */
    public function __construct($purifier)
    {
        $this->htmlPurifier = $purifier;
    }

    /**
     * Gets the html purifier
     *
     * @return HTMLPurifier
     */
    public function getHtmlPurifier()
    {
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
        return $htmlPurifier->purify($value);
    }
}
