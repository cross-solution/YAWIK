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

class JsonSearchResult implements FilterInterface
{
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

