<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Organizations\Entity;

use Core\Entity\AbstractIdentifiableHydratorAwareEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Defines the contact address of an organization
 *
 * @ODM\EmbeddedDocument
 */
class WorkflowSettings extends AbstractIdentifiableHydratorAwareEntity implements WorkflowSettingsInterface
{
    /**
     * Accept application by department manager
     *
     * @var bool
     * @ODM\Field(type="bool") */
    protected $acceptApplicationByDepartmentManager = true;


    /**
     * Accept application by department manager
     *
     * @var bool
     * @ODM\Field(type="bool") */
    protected $assignDepartmentManagersToJobs = true;

    /**
     * Sets AcceptApplicationByDepartmentManager
     *
     * @param bool $acceptApplicationByDepartmentManager
     * @return WorkflowSettings
     */
    public function setAcceptApplicationByDepartmentManager($acceptApplicationByDepartmentManager)
    {
        $this->acceptApplicationByDepartmentManager= $acceptApplicationByDepartmentManager;
        return $this;
    }
    
    /**
     * Gets AcceptApplicationByDepartmentManager
     *
     * @return bool
     */
    public function getAcceptApplicationByDepartmentManager()
    {
        return $this->acceptApplicationByDepartmentManager;
    }

    /**
     * Sets AssignDepartmentManagersToJobs
     *
     * @param bool $assignDepartmentManagersToJobs
     * @return WorkflowSettings
     */
    public function setAssignDepartmentManagersToJobs($assignDepartmentManagersToJobs)
    {
        $this->assignDepartmentManagersToJobs = $assignDepartmentManagersToJobs;
        return $this;
    }

    /**
     * Gets AssignDepartmentManagersToJobs
     *
     * @return bool
     */
    public function getAssignDepartmentManagersToJobs()
    {
        return $this->assignDepartmentManagersToJobs;
    }
}
