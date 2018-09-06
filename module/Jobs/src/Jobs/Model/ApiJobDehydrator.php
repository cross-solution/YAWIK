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
use Jobs\View\Helper\JobUrl;

class ApiJobDehydrator
{
    /**
     * ViewHelper for generating an url
     *
     * @var Url $url
     */
    protected $url;

  /**
   * ViewHelper for generating an url to a job posting
   *
   * @var JobUrl $jobUrl
   */
    protected $jobUrl;

    /**
     * @param Url $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

   /**
    * @param JobUrl $url
    *
    * @return $this
    */
    public function setJobUrl($url)
    {
        $this->jobUrl = $url;
        return $this;
    }

    /**
     * @param Job $job
     *
     * @return array
     */
    public function dehydrate(Job $job)
    {
        return array(
            'datePublishStart' => $job->getDatePublishStart(),
            'title' => $job->getTitle(),
            'location' => $job->getLocation(),
            'link' => $this->jobUrl->__invoke(
                $job,[
                  'linkOnly'=> true,
                  'absolute' => true,
                ]
            ),
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
