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
 * template viewmodel form
 * Class viewModelTemplateFilterForm
 * @package Jobs\Filter
 */
class viewModelTemplateFilterForm extends viewModelTemplateFilterAbstract
{
    protected $viewHelperForm;

    public function setViewHelperForm($viewHelperForm)
    {
        $this->viewHelperForm = $viewHelperForm;
        return $this;
    }

    protected function getViewHelperForm()
    {
        return $this->viewHelperForm;
    }

    protected function extract ($form)
    {
        $job = $form->getEntity();
        $this->job = $job;
        $this->setUriApply();
        $this->setOrganizationInfo();
        $this->setLocation();
        $this->setDescription();

        $formDescription = $form->get('descriptionFormDescription');
        $formBenefits = $form->get('descriptionFormBenefits');
        $formRequirements = $form->get('descriptionFormRequirements');
        $formQualifications = $form->get('descriptionFormQualifications');
        $descriptionFormTitle = $form->get('descriptionFormTitle');

        $viewHelperForm = $this->getViewHelperForm();

        $this->container['descriptionEditable'] = $viewHelperForm->render($formDescription);
        $this->container['benefits'] = $viewHelperForm->render($formBenefits);
        $this->container['requirements'] = $viewHelperForm->render($formRequirements);
        $this->container['qualifications'] = $viewHelperForm->render($formQualifications);
        $this->container['title'] = $viewHelperForm->render($descriptionFormTitle);
        $this->container['headTitle'] = $job->templateValues->title;
        return $this;
    }
}