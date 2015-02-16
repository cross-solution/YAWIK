<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Jobs\Entity\Job;

class ApplyUrl extends AbstractHelper
{
    protected $urlHelper;
    protected $translateHelper;
    protected $paramsHelper;
    protected $serverUrlHelper;

    public function setUrlHelper($helper)
    {
        $this->urlHelper = $helper;
        return $this;
    }

    public function setTranslateHelper($helper)
    {
        $this->translateHelper = $helper;
        return $this;
    }

    public function setParamsHelper($helper)
    {
        $this->paramsHelper = $helper;
        return $this;
    }

    public function setServerUrlHelper($helper)
    {
        $this->serverUrlHelper = $helper;
        return $this;
    }

    public function __invoke(Job $jobEntity)
    {
        $result = '';
        if ($jobEntity->getAtsEnabled() == True && !empty($jobEntity->uriApply)) {
            $result = '<a href="' .  $jobEntity->uriApply . '">' . call_user_func_array($this->translateHelper, array('Apply')) . '</a>';
        }
        $contactEmail = $jobEntity->contactEmail;
        if (($jobEntity->getAtsEnabled() == False && !empty($contactEmail)) || ($jobEntity->getAtsEnabled() == True && empty($jobEntity->uriApply))) {
            $url = call_user_func_array($this->urlHelper,
                array( 'lang/apply',
                    array('applyId' => $jobEntity->applyId ,
                    'lang' => call_user_func_array($this->paramsHelper, array('lang')))));
            $query = http_build_query(array('subscriberUri' => call_user_func_array($this->serverUrlHelper,array()) . '/subscriber/' . 1));

            $result = '<a href="' .  $url . '?' . $query . '">' . call_user_func_array($this->translateHelper, array('Apply')) . '</a>';
        }

        return $result;
    }
}