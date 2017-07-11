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
 * template viewmodel form
 *
 * Class viewModelTemplateFilterForm
 * @package Jobs\Filter
 */
class ViewModelTemplateFilterForm extends ViewModelTemplateFilterAbstract
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

    protected function extract($form)
    {
        $job = $form->getEntity();
        $this->job = $job; /* @var \Jobs\Entity\Job $job */
        $this->getJsonLdHelper()->setJob($job);
        $this->setApplyData();
        $this->setOrganizationInfo();
        $this->setLocation();
        $this->setDescription();
        $this->setTemplateDefaultValues();

        $formDescription = $form->get('descriptionFormDescription');
        $formBenefits = $form->get('descriptionFormBenefits');
        $formRequirements = $form->get('descriptionFormRequirements');
        $formLabelRequirements = $form->get('templateLabelRequirements');
        $formLabelQualifications = $form->get('templateLabelQualifications');
        $formLabelBenefits = $form->get('templateLabelBenefits');
        $formQualifications = $form->get('descriptionFormQualifications');
        $descriptionFormTitle = $form->get('descriptionFormTitle');
        $descriptionFormHtml  = $form->get('descriptionFormHtml');

        $viewHelperForm = $this->getViewHelperForm();

        $this->container['descriptionEditable'] = $viewHelperForm->render($formDescription);
        $this->container['benefits'] = $viewHelperForm->render($formBenefits);
        $this->container['requirements'] = $viewHelperForm->render($formRequirements);
        $this->container['labelRequirements'] = $viewHelperForm->render($formLabelRequirements);
        $this->container['labelQualifications'] = $viewHelperForm->render($formLabelQualifications);
        $this->container['labelBenefits'] = $viewHelperForm->render($formLabelBenefits);
        $this->container['qualifications'] = $viewHelperForm->render($formQualifications);
        $this->container['title'] = $viewHelperForm->render($descriptionFormTitle);
        $this->container['headTitle'] = $job->getTemplateValues()->getTitle();
        $this->container['html'] = $viewHelperForm->render($descriptionFormHtml);

        $this->container['jobId'] = $job->getId();
        $this->container['uriJob'] = $this->urlPlugin->fromRoute(
            'lang/jobs/view',
            [],
            [
                'query' => [ 'id' => $job->getId() ],
                'force_canonical' => true
            ]
        );

        return $this;
    }
}
