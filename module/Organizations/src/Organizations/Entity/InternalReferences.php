<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Organizations\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\AbstractEntity;
use Doctrine\Common\Collections\Collection;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 * @ODM\EmbeddedDocument
 * @since 0.18
 */
class InternalReferences extends AbstractEntity
{
    /**
     * User ids of all employees.
     *
     * @var array
     * @ODM\Collection
     */
    protected $employees = array();

    public function setEmployeeIds(array $employees)
    {
        $this->employees = $employees;

        return $this;
    }

    public function getEmployeeIds()
    {
        return $this->employees;
    }

    public function setEmployeesIdsFromCollection(Collection $employees)
    {
        $ids = array();

        foreach ($employees as $emp) {
            $ids[] = $emp->getUser()->getId();
        }

        return $this->setEmployeeIds($ids);
    }
}