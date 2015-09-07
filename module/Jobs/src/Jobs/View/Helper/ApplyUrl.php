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

/**
 * View helper to assemble an apply link according to the ATS configuration in a job entity.
 *
 * @author Mathias Weitz <weitz@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo   write test
 */
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
        $ats = $jobEntity->getAtsMode();

        if ($ats->isDisabled()) {
            return '';
        }

        if ($ats->isIntern() || $ats->isEmail()) {
            $urlHelper = $this->urlHelper;
            $serverUrlHelper = $this->serverUrlHelper;
            $params = $this->paramsHelper;
            $query = array('subscriberUri' => $serverUrlHelper(array()) . '/subscriber/' . 1);
            $route = 'lang/apply';
            $params = array('applyId' => $jobEntity->getApplyId(), 'lang' => $params('lang'));
            $url = $urlHelper($route, $params, array('query' => $query));
        } else {
            $url = $ats->getUri();
        }
        $translate = $this->translateHelper;
        $result = sprintf('<a href="%s">%s</a>', $url, $translate('Apply'));

        return $result;
    }
}
