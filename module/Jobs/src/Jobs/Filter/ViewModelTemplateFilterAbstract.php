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

use Jobs\Entity\Job;
use Zend\Filter\FilterInterface;
use Zend\View\Model\ViewModel;
use Zend\I18n\Translator\TranslatorInterface as Translator;

/**
 * assembles a ViewModel for job templates.
 * this class needs to be extended for specific assignments
 * Class viewModelTemplateFilterAbstract
 * @package Jobs\Filter
 */
abstract class ViewModelTemplateFilterAbstract implements FilterInterface
{

    /**
     * @var array assembles all data for the viewmodel
     */
    protected $container;

    /**
     * @var Job
     */
    protected $job;

    /**
     * @var array
     */
    protected $config;

    /**
     * creating absolute links like the apply-link
     * absolute links are needed on the server of the provider
     * @var
     */
    protected $urlPlugin;

    /**
     * also needed to create absolute links
     * @var
     */
    protected $basePathHelper;

    /**
     * @var
     */
    protected $serverUrlHelper;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @param $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return;
    }

    /**
     * @param $urlPlugin
     */
    public function setUrlPlugin($urlPlugin)
    {
        $this->urlPlugin = $urlPlugin;
        return;
    }

    /**
     * @param $basePathHelper
     */
    public function setBasePathHelper($basePathHelper)
    {
        $this->basePathHelper = $basePathHelper;
        return;
    }

    /**
     * @return mixed
     */
    public function getBasePathHelper()
    {
        return $this->basePathHelper;
    }

    /**
     * @param $serverUrlHelper
     */
    public function setServerUrlHelper($serverUrlHelper)
    {
        $this->serverUrlHelper = $serverUrlHelper;
        return;
    }

	/**
     * @return mixed
     */
    public function getServerUrlHelper()
    {
        return $this->serverUrlHelper;
    }

    /**
	 * @param Translator $translator
	 * @return ViewModelTemplateFilterAbstract
	 */
	public function setTranslator(Translator $translator)
	{
		$this->translator = $translator;
		
		return $this;
	}

    /**
     * @param mixed $value
     * @return mixed|ViewModel
     * @throws \InvalidArgumentException
     */
    public function filter($value)
    {
        $model = new ViewModel();
        $this->container = array();
        $this->extract($value);
        $model->setVariables($this->container);
        if (!isset($this->job)) {
            throw new \InvalidArgumentException('cannot create a viewModel for Templates without an $job');
        }
        $model->setTemplate('templates/' . $this->job->getTemplate() . '/index');
        return $model;
    }

    /**
     * should be overwritten
     * here are all assignments to container arr administered
     * input-attributes are the job and the configuration
     * output-attribute is the container
     * @param $value
     * @return mixed
     */
    abstract protected function extract($value);

    /**
     * Set the apply buttons of the job posting
     *
     * @return ViewModelTemplateFilterAbstract
     * @throws \InvalidArgumentException
     */
    protected function setApplyButtons()
    {
        if (!isset($this->job)) {
            throw new \InvalidArgumentException('cannot create a viewModel for Templates without a $job');
        }
        
        $this->container['applyButtons'] = [];
        $atsMode = $this->job->getAtsMode();
        $defaultUrl = null;
        
        if ($atsMode->isIntern() || $atsMode->isEmail()) {
            $defaultUrl = $this->urlPlugin->fromRoute('lang/apply', ['applyId' => $this->job->getApplyId()], ['force_canonical' => true]);
        } elseif ($atsMode->isUri()) {
            $defaultUrl = $atsMode->getUri();
        }
        
        if ($defaultUrl) {
            $this->container['applyButtons'][] = [
                'label' => $this->translator->translate('Apply now'),
                'url' => $defaultUrl,
                'type' => 'default'
            ];
        }
        
        if ($atsMode->isIntern() && $atsMode->getOneClickApply()) {
            $this->container['applyButtons'][] = [
                'label' => $this->translator->translate('One click apply'),
                'url' => $this->urlPlugin->fromRoute('lang/apply-one-click', ['applyId' => $this->job->getApplyId()], ['force_canonical' => true]),
                'type' => 'one-click'
            ];
        }
        
        return $this;
    }

    /**
     * Sets the location of a jobs
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    protected function setLocation()
    {
        if (!isset($this->job)) {
            throw new \InvalidArgumentException('cannot create a viewModel for Templates without aa $job');
        }
        $location = $this->job->getLocation();
        $this->container['location'] = isset($location)?$location:'';
        return $this;
    }

    /**
     * Sets the company description of a job. Use the description of an organization as default
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    protected function setDescription()
    {
        if (!isset($this->job)) {
            throw new \InvalidArgumentException('cannot create a viewModel for Templates without a $job');
        }

        if (empty($this->job->templateValues->description) && isset($this->job->organization)) {
            $this->job->templateValues->description = $this->job->organization->description;
        }
        $description = $this->job->templateValues->description;

        $this->container['description'] = isset($description)?$description:'';
        return $this;
    }

    /**
     * Sets the organizations contact address
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    protected function setOrganizationInfo()
    {
        if (!isset($this->job)) {
            throw new \InvalidArgumentException('cannot create a viewModel for Templates without a $job');
        }
        $organizationName = '';
        $organizationStreet = '';
        $organizationPostalCode = '';
        $organizationPostalCity = '';
        $organization = $this->job->organization;
        $user = $this->job->getUser();

        if (isset($organization)) {
            $organizationName = $organization->organizationName->name;
            $organizationStreet = $organization->contact->street.' '.$organization->contact->houseNumber;
            $organizationPostalCode = $organization->contact->postalcode;
            $organizationPostalCity = $organization->contact->city;
            $organizationPhone = $organization->contact->phone;
            $organizationFax = $organization->contact->fax;
        } else {
            $organizationName =
            $organizationStreet =
            $organizationPostalCode =
            $organizationPostalCity =
            $organizationPhone =
            $organizationFax = '';
        }
        $this->container['contactEmail'] = $user ? $user->getInfo()->getEmail() : '';
        $this->container['organizationName'] = $organizationName;
        $this->container['street'] = $organizationStreet;
        $this->container['postalCode'] = $organizationPostalCode;
        $this->container['city'] = $organizationPostalCity;
        $this->container['phone'] = $organizationPhone;
        $this->container['fax'] = $organizationFax;

        if (isset($organization) && isset($organization->image) && $organization->image->uri) {
            $this->container['uriLogo'] = $this->makeAbsolutePath($organization->image->uri);
        } else {
            $this->container['uriLogo'] = $this->makeAbsolutePath($this->config->default_logo);
        }
        return $this;
    }

    /**
     * Sets the default values of an organizations job template
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    protected function setTemplateDefaultValues()
    {
        if (!isset($this->job)) {
            throw new \InvalidArgumentException('cannot create a viewModel for Templates without a $job');
        }
        $labelQualifications='';
        $labelBenefits='';
        $labelRequirements='';

        $organization = $this->job->organization;
        if (isset($organization)) {
            $labelRequirements = $organization->getTemplate()->getLabelRequirements();
            $labelQualifications = $organization->getTemplate()->getLabelQualifications();
            $labelBenefits = $organization->getTemplate()->getLabelBenefits();
        }
        $this->container['labelRequirements'] = $labelRequirements;
        $this->container['labelQualifications'] = $labelQualifications;
        $this->container['labelBenefits'] = $labelBenefits;

        return $this;
    }

    /**
     * Sets the template
     *
     * @return $this
     */
    protected function setTemplate()
    {
        $this->container['templateName'] = $this->job->template;
        return $this;
    }

    /**
     * combines two helper
     *
     * @param $path
     * @return mixed
     */
    protected function makeAbsolutePath($path)
    {
        $path = $this->serverUrlHelper->__invoke($this->basePathHelper->__invoke($path));
        return $path;
    }
}
