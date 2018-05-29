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
use Jobs\View\Helper\JsonLd;
use Zend\Filter\FilterInterface;
use Zend\View\Model\ViewModel;

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
     *
     * @var $urlPlugin \Zend\Mvc\Controller\Plugin\Url
     */
    protected $urlPlugin;

    /**
     * also needed to create absolute links
     * @var
     */
    protected $basePathHelper;

    /**
     * @var $serverUrlHelper \Zend\View\Helper\ServerUrl
     */
    protected $serverUrlHelper;

    /**
     * @var $imageFileCacheHelper \Organizations\ImageFileCache\Manager
     */
    protected $imageFileCacheHelper;

    /**
     *
     *
     * @var JsonLd
     */
    protected $jsonLdHelper;

    /**
     * @param \Jobs\View\Helper\JsonLd $jsonLdHelper
     *
     * @return self
     */
    public function setJsonLdHelper(JsonLd $jsonLdHelper)
    {
        $this->jsonLdHelper = $jsonLdHelper;

        return $this;
    }

    /**
     * @return \Jobs\View\Helper\JsonLd
     */
    public function getJsonLdHelper()
    {
        return $this->jsonLdHelper;
    }

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
     * @param $basePathHelper \Zend\View\Helper\Basepath
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
     * @return mixed
     */
    public function setImageFileCacheHelper($imageFileCacheHelper)
    {
        $this->imageFileCacheHelper=$imageFileCacheHelper;
        return;
    }

    /**
     * @return mixed
     */
    public function getImageFileCacheHelper()
    {
        return $this->imageFileCacheHelper;
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
        $this->container['job'] = $this->job;
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
    protected function setApplyData()
    {
        if (!isset($this->job)) {
            throw new \InvalidArgumentException('cannot create a viewModel for Templates without a $job');
        }
        
        $data = [
            'applyId' => $this->job->getApplyId(),
            'uri' => null,
            'oneClickProfiles' => []
        ];
        $atsMode = $this->job->getAtsMode();
        
        if ($atsMode->isIntern() || $atsMode->isEmail()) {
            $data['uri'] = $this->urlPlugin->fromRoute('lang/apply', ['applyId' => $this->job->getApplyId()], ['force_canonical' => true]);
        } elseif ($atsMode->isUri()) {
            $data['uri'] = $atsMode->getUri();
        }
        
        if ($atsMode->isIntern() && $atsMode->getOneClickApply()) {
            $data['oneClickProfiles'] = $atsMode->getOneClickApplyProfiles();
        }
        
        $this->container['applyData'] = $data;
        
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

        if (empty($this->job->getTemplateValues()->getDescription()) && is_object($this->job->getOrganization())) {
            $this->job->getTemplateValues()->setDescription($this->job->getOrganization()->getDescription());
        }
        $description = $this->job->getTemplateValues()->getDescription();

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
        $organization = $this->job->getOrganization();
        $user = $this->job->getUser();

        if (isset($organization)) {
            $organizationName = $organization->getOrganizationName()->getName();
            $organizationStreet = $organization->getContact()->getStreet().' '.$organization->getContact()->getHouseNumber();
            $organizationPostalCode = $organization->getContact()->getPostalcode();
            $organizationPostalCity = $organization->getContact()->getCity();
            $organizationPhone = $organization->getContact()->getPhone();
            $organizationFax = $organization->getContact()->getFax();
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

        if (is_object($organization) && is_object($organization->getImage()) && $organization->getImage()->getUri()) {
            $this->container['uriLogo'] = $this->basePathHelper->__invoke($this->imageFileCacheHelper->getUri($organization->getImage(true)));
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

        $organization = $this->job->getOrganization();
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
        $this->container['templateName'] = $this->job->getTemplate();
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
