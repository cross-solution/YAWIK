<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Auth\Options\ModuleOptions;

class SocialButtons extends AbstractHelper
{
    /**
     * @var ModuleOptions $options;
     */
    protected $options;

    /**
     * @var array $options;
     */
    protected $config;

    /**
     * @param $options ModuleOptions
     * @param $config array
     */
    public function __construct($options, $config)
    {
        $this->options = $options;
        $this->config = $config;
        return $this;
    }

    /**
     * returns the advertised title
     *
     * @return array
     */
    public function __invoke()
    {
        $SocialNetworksEnabled=[];
        foreach ($this->config['hybridauth'] as $key => $val) {
            if ($val['enabled'] and in_array(strtolower($key), $this->options->getEnableLogins())) {
                $SocialNetworksEnabled[strtolower($key)] = $key;
            }
        }
        return $SocialNetworksEnabled;
    }
}
