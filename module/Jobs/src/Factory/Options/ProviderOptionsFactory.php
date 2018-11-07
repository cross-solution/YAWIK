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

use Interop\Container\ContainerInterface;
use Jobs\Options\ProviderOptions;
use Zend\ServiceManager\Factory\FactoryInterface;

class ProviderOptionsFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $multiposting = array_key_exists('multiposting', $config) ? $config['multiposting'] : array();

        $providerOptions = new ProviderOptions();

        if (array_key_exists('channels', $multiposting)) {
            foreach ($multiposting['channels'] as $channelName => $channel) {
                $channelOptions = $container->get('Jobs/Options/Channel');
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
