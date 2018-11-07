<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
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
}
