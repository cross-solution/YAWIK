<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Factory\Options;

use Jobs\Options\ProviderOptions;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ProviderOptionsFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $multiposting = array_key_exists('multiposting', $config) ? $config['multiposting'] : array();

        $providerOptions = new ProviderOptions();

        if (array_key_exists('channels', $multiposting)) {
            foreach ($multiposting['channels'] as $channelName => $channel) {
                $channelOptions = $serviceLocator->get('Jobs/Options/Channel');
                if (empty($channelOptions->key)) {
                    $channelOptions->key = $channelName;
                }
                $channelOptions->setFromArray($channel);
                $providerOptions->addChannel($channelOptions);
            }
        }
        return $providerOptions;
    }
}
