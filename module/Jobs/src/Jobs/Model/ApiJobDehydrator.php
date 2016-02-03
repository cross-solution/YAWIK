<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

namespace Jobs\Model;

use Jobs\Entity\Job;
use Zend\View\Helper\Url;

class ApiJobDehydrator
{
    /**
     * Url View Helper
     *
     * @var url URL
     */
    protected $url;

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param Job $job
     * @return array
     */
    public function dehydrate(Job $job)
    {
        return array(
            'title' => $job->getTitle(),
            'location' => $job->getLocation(),
            'link' => $this->url->__invoke(
                'lang/jobs/view',
                [],
                [
                    'query'=>['id' => $job->getId()],
                    'force_canonical'=>true
                ]),
            'organization' => array(
                'name' => $job->getOrganization()->getOrganizationName()->getName(),
            ),
            'template_values' => array(
                'requirements' => $job->getTemplateValues()->getRequirements(),
                'qualification' => $job->getTemplateValues()->getQualifications(),
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