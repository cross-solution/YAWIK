<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
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
     * Accept application by recruiters, forward to department manager
     *
     * @var bool
     * @ODM\Field(type="bool")
     */
    protected $acceptApplicationByRecruiters = false;

    /**
     * Sets AcceptApplicationByDepartmentManager
     *
     * Will set acceptApplicationByRecruiters to _false_.
     *
     * @param bool $acceptApplicationByDepartmentManager
     * @return WorkflowSettings
     */
    public function setAcceptApplicationByDepartmentManager($acceptApplicationByDepartmentManager)
    {
        $this->acceptApplicationByDepartmentManager = $acceptApplicationByDepartmentManager;
        $acceptApplicationByDepartmentManager && $this->acceptApplicationByRecruiters = false;
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

    public function setAcceptApplicationByRecruiters(bool $flag): void
    {
        $this->acceptApplicationByRecruiters = $flag;
        $flag && $this->acceptApplicationByDepartmentManager = false;
    }

    public function getAcceptApplicationByRecruiters(): bool
    {
        return $this->acceptApplicationByRecruiters;
    }
}
