<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** PaginationQuery.php */
namespace Cv\Repository\Filter;

use Zend\Filter\FilterInterface;
use Core\Repository\Filter\AbstractPaginationQuery;
use Auth\Entity\UserInterface;

class PaginationQuery extends AbstractPaginationQuery
{
    protected $repositoryName = 'Cv/Cv';
    protected $user;
    
    public function __construct(UserInterface $user = null)
    {
        $this->user = $user;
    }
    
    public function createQuery($params, $queryBuilder)
    {
        $by = $params->get('by', 'me');
        if ('me' == $by && $this->user) {
            $queryBuilder->field('user')->equals($this->user->id);
        }
        return $queryBuilder->getQuery();
    }
}
