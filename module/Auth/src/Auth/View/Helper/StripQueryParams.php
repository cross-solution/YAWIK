<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Auth view helper */
namespace Auth\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Auth\Filter\StripQueryParams as StripQueryParamsFilter;

/**
 * View helper to access authentication service and the 
 * authenticated user (and its properties).
 * 
 */
class StripQueryParams extends AbstractHelper
{
    
    protected $filter;
    
    public function setFilter($filter)
    {
        $this->filter = $filter;
        return $this;
    }
    
    public function getFilter()
    {
        if (!$this->filter) {
            $this->setFilter(new StripQueryParamsFilter());
        }
        return $this->filter;
    }
    
    /**
     * Entry point.
     * 
     * Returns itself if called without arguments.
     * Returns a property value of the authenticated user or null, if
     * no user is authenticated or the property does not exists.
     * 
     * @param string $property
     * @return \Auth\View\Helper\Auth|NULL
     */
    public function __invoke($uri, array $stripParams = null)
    {
        if (null === $stripParams) {
            return $this->getFilter()->filter($uri);
        }
        return $this->getFilter()->filter($uri, $stripParams);
    }
}