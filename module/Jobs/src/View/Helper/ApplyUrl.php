<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Jobs\Entity\JobInterface as Job;

/**
 * Renders the link the an application form according to passed $options
 *   linkOnly: Returns the relative link only
 *   absolute: Make the link absolute
 *
 * Usage example with defaults:
 * <code>
 *      <?=$this->applyUrl($job, ['linkOnly'=>true, 'absolute' => true])?>
 * </code>
 */
/**
 * View helper to assemble an apply link according to the ATS configuration in a job entity.
 *
 * @method \Core\View\Helper\Params paramsHelper()
 *
 * @author Mathias Weitz <weitz@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @todo   write test
 */
class ApplyUrl extends AbstractHelper
{
    /**
     * Default options
     *
     * @var array
     */
    protected $options = [
        'absolute' => false,
        'linkOnly' => false
    ];

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

    public function __invoke(Job $jobEntity, $options = [])
    {
        $options= array_merge($this->options, $options);

        $ats = $jobEntity->getAtsMode();

        if ($ats->isDisabled()) {
            return '';
        }

        $paramsHelper = $this->paramsHelper;
        $serverUrlHelper = $this->serverUrlHelper;
        $urlHelper = $this->urlHelper;

        if ($ats->isIntern() || $ats->isEmail()) {
            $query = [ 'subscriberUri' => $serverUrlHelper(array()) . '/subscriber/' . 1 ];
            $route = 'lang/apply';
            $params = [
                'applyId' => $jobEntity->getApplyId(),
                'lang' => $paramsHelper('lang'),
            ];
            if ($paramsHelper('channel')) {
                $params['channel'] = $paramsHelper('channel');
            }

            $url = $urlHelper($route, $params, array('query' => $query));
        } else {
            $url = $ats->getUri();
        }

        if ($options['linkOnly']) {
            $result=$url;
            if ($options['absolute'] && !preg_match('~^https?://~', $url)) {
                $result = $serverUrlHelper($url);
            }
        } else {
            $translate = $this->translateHelper;
            $result = sprintf('<a href="%s" rel="nofollow">%s</a>', $url, $translate('Apply'));
        }

        return $result;
    }

    /**
     * @param $options
     */
    public function setOptions($options)
    {
        foreach ($options as $key=>$val) {
            if (array_key_exists($this->options, $key)) {
                $this->options[$key]=$val;
            }
        }
    }
}
