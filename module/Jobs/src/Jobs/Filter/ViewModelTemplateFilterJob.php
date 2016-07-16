<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Filter;

/**
 * template ViewModel html
 *
 * Class ViewModelTemplateFilterJob
 * @package Jobs\Filter
 */
class ViewModelTemplateFilterJob extends ViewModelTemplateFilterAbstract
{
    /**
     * assign the form-elements to the template
     * @param \Jobs\Entity\Job $job
     * @return $this
     */
    protected function extract($job)
    {
        $this->job = $job;
        $this->setApplyData();
        $this->setOrganizationInfo();
        $this->setLocation();
        $this->setDescription();
        $this->setTemplate();
        $this->setTemplateDefaultValues();

        $this->container['descriptionEditable'] = $job->getTemplateValues()->getDescription();
        $this->container['benefits'] = $job->getTemplateValues()->getBenefits();
        $this->container['requirements'] = $job->getTemplateValues()->getRequirements();
        $this->container['qualifications'] = $job->getTemplateValues()->getQualifications();
        $this->container['title'] = $job->getTemplateValues()->getTitle();
        $this->container['headTitle'] = strip_tags($job->getTemplateValues()->getTitle());
        $this->container['uriApply'] = $this->container['applyData']['uri'];
        $this->container['contactEmail'] = strip_tags($job->getContactEmail());
        return $this;
    }
}
