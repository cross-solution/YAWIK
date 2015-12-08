<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Filter;

/**
 * template viewmodel html
 * Class ViewModelTemplateFilterJob
 * @package Jobs\Filter
 */
class ViewModelTemplateFilterJob extends ViewModelTemplateFilterAbstract
{
    /**
     * assign the form-elements to the template
     * @param $job
     * @return $this
     */
    protected function extract($job)
    {
        $this->job = $job;
        $this->setUriApply();
        $this->setOrganizationInfo();
        $this->setLocation();
        $this->setDescription();
        $this->setTemplate();

        $this->container['descriptionEditable'] = $job->templateValues->description;
        $this->container['benefits'] = $job->templateValues->benefits;
        $this->container['requirements'] = $job->templateValues->requirements;
        $this->container['qualifications'] = $job->templateValues->qualifications;
        $this->container['title'] = $job->templateValues->title;
        $this->container['headTitle'] = strip_tags($job->templateValues->title);

        $this->container['contactEmail'] = strip_tags($job->contactEmail);
        return $this;
    }
}
