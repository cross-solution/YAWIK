<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** JsonSearchResult.php */ 
namespace Auth\Entity\Filter;

use Zend\Filter\FilterInterface;
use Auth\Entity\User;

/**
 * Filters an user to a search result array.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class UserToSearchResult implements FilterInterface
{
    /**
     * Filters an user to a search result array.
     * 
     * @return array
     * @see \Zend\Filter\FilterInterface::filter()
     */
    public function filter($value)
    {
        if (!$value instanceOf User) {
            return array();
        }
        
        return array(
            'id' => $value->id,
            'name' => $value->info->displayName,
            'image' => $value->info->image ? $value->info->image->uri : '',
            'company' => 'Dummy Company',
            'position' => 'Dummy position',
        );
    }
}

