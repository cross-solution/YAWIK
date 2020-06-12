<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 */

namespace Organizations\Entity;

interface WorkflowSettingsInterface
{
    /**
     * Sets AcceptApplicationByDepartmentManager
     *
     * @param bool $acceptApplicationByDepartmentManager
     * @return WorkflowSettings
     */
    public function setAcceptApplicationByDepartmentManager($acceptApplicationByDepartmentManager);

    /**
     * Gets AcceptApplicationByDepartmentManager
     *
     * @return bool
     */
    public function getAcceptApplicationByDepartmentManager();

    /**
     * Sets AssignDepartmentManagersToJobs
     *
     * @param bool $assignDepartmentManagersToJobs
     * @return WorkflowSettings
     */
    public function setAssignDepartmentManagersToJobs($assignDepartmentManagersToJobs);

    /**
     * Gets AssignDepartmentManagersToJobs
     *
     * @return bool
     */
    public function getAssignDepartmentManagersToJobs();

    public function setAcceptApplicationByRecruiters(bool $flag): void;
    public function getAcceptApplicationByRecruiters(): bool;
}
