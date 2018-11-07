<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** AbstractPaginationQuery.php */
namespace Core\Repository\Filter;

use Zend\Filter\FilterInterface;

/**
 * Class AbstractPaginationQuery
 * @package Core\Repository\Filter
 */
abstract class AbstractPaginationQuery implements FilterInterface
{

    /**
     * @var
     */
    protected $repositoryName;

    /**
     * @var array
     */
    protected $sortPropertiesMap = array();

    /**
     * @param mixed $value
     * @param null $queryBuilder
     * @return mixed
     * @throws \DomainException
     */
    public function filter($value, $queryBuilder = null)
    {
        if (null === $queryBuilder) {
            throw new \DomainException('$queryBuilder must not be null');
        }
        
        if ($this->repositoryName) {
            $nameParts = explode('/', $this->repositoryName);
            $entityClass = $nameParts[0] . '\\Entity\\' . $nameParts[1];
            $queryBuilder->find($entityClass);
        }
        return $this->createQuery($value, $queryBuilder);
    }

    /**
     * @param $params
     * @param $queryBuilder
     * @return mixed
     */
    abstract public function createQuery($params, $queryBuilder);

    /**
     * @param $sort
     * @return array
     */
    protected function filterSort($sort)
    {
        if ('-' == $sort{0}) {
            $sortProp = substr($sort, 1);
            $sortDir  = -1;
        } else {
            $sortProp = $sort;
            $sortDir = 1;
        }

        if (isset($this->sortPropertiesMap[$sortProp])) {
            $sortProp = $this->sortPropertiesMap[$sortProp];

            if (is_array($sortProp)) {
                return array_fill_keys($sortProp, $sortDir);
            }
        }

        return array($sortProp => $sortDir);
    }
}
