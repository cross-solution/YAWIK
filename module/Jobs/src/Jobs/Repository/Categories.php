<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Repository;

use Core\Repository\AbstractRepository;

/**
 * Repository for job categories.
 *
 * Creates default categories upon first access, if not present.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class Categories extends AbstractRepository
{

    public function findOneBy(array $criteria)
    {
        $category = parent::findOneBy($criteria);

        return $category ?: $this->createDefaultCategory($criteria);
    }

    public function findBy(array $criteria, array $sort = null, $limit = null, $skip = null)
    {
        $categories = parent::findBy($criteria, $sort, $limit, $skip);

        return empty($categories) ? [$this->createDefaultCategory($criteria)] : $categories;
    }

    /**
     * Creates and stores the default category hirarchy for the given value.
     *
     * @param array|string $value
     *
     * @return null|\Jobs\Entity\Category
     */
    public function createDefaultCategory($value)
    {
        if (is_array($value)) {
            $value = isset($value['value']) ? $value['value'] : '';
        }

        if ('professions' != $value && 'employmentTypes' != $value && 'industries' != $value) {
            return null;
        }

        $builder = $this->getService('Jobs/DefaultCategoriesBuilder');

        $category = $builder->build($value);

        $this->store($category);

        return $category;
    }
}