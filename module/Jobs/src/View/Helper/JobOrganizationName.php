<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2018 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\View\Helper;

use Jobs\Entity\JobInterface;
use Zend\View\Helper\AbstractHelper;

/**
 * Returns the organization name for a job.
 *
 * It will determine the name in the following order
 *
 * 1. the value stored in the job's copmany field.
 * 2. the organization name of the associated organization.
 * 3. the default value
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class JobOrganizationName extends AbstractHelper
{

    /**
     * Get the organization name.
     *
     * @param JobInterface $job
     * @param string  $default
     *
     * @return string
     */
    public function __invoke(JobInterface $job, $default = '')
    {
        /* @var \Jobs\Entity\Job $job */
        if ($orgName = $job->getCompany(/*useOrganizationEntity*/ false)) {
            return $orgName;
        }

        if ($org = $job->getOrganization()) {
            return $org->getOrganizationName()->getName();
        }

        return $default;
    }
}
