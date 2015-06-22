<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Factory\Options;

use Jobs\Options\ChannelOptions;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ChannelOptionsFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $channel = new ChannelOptions();
        /* @var $core \Core\Options\ModuleOptions */
        $core = $serviceLocator->get("Core/Options");

        if ('' == $channel->getCurrency()) {
            $currency=$core->getDefaultCurrencyCode();
            $channel->setCurrency( $currency );
        }

        if ('' == $channel->getTax()) {
            $channel->setTax( $core->getDefaultTaxRate() );
        }

        return $channel;
    }
}
