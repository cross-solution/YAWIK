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
use Jobs\Options\ChannelOptions;
use Zend\ServiceManager\Factory\FactoryInterface;

class ChannelOptionsFactory implements FactoryInterface
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
        $channel = new ChannelOptions();

        /* @var $core \Core\Options\ModuleOptions */
        $core = $container->get("Core/Options");

        if ('' == $channel->getCurrency()) {
            $currency=$core->getDefaultCurrencyCode();
            $channel->setCurrency($currency);
        }

        if ('' == $channel->getTax()) {
            $channel->setTax($core->getDefaultTaxRate());
        }
        return $channel;
    }
}
