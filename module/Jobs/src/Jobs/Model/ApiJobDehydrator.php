<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */

namespace Jobs\Model;

use Jobs\Entity\Job;

class ApiJobDehydrator
{
    /**
     * @param Job $job
     * @return array
     */
    public function dehydrate(Job $job)
    {
        return array(
            'title' => $job->getTitle(),
            'location' => $job->getLocation(),
            'organization' => array(
                'name' => $job->getOrganization()->getOrganizationName()->getName(),
            ),
            'template_values' => array(
                'requirements' => $job->getTemplateValues()->getRequirements(),
                'qualification' => $job->getTemplateValues()->getQualification(),
                'benefits' => $job->getTemplateValues()->getBenefits()
            )
        );
    }

    /**
     * @param Job[] $jobs
     * @return array
     */
    public function dehydrateList(array $jobs)
    {
        $result = [];
        foreach ($jobs as $job) {
            $result[] = $this->dehydrate($job);
        }

        return $result;
    }
}