<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
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

        $info = $value->getInfo();

        return array(
            'id' => $value->getId(),
            'name' => $info->displayName,
            'image' => $info->image ? $info->image->uri : '',
            'email' => $info->email,
        );
    }
}