<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Queue\Strategy;

use Interop\Container\ContainerInterface;
use Psr\Log\NullLogger;
use Zend\Log\LoggerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for \Core\Queue\Strategy\LogStrategy
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test  
 */
class LogStrategyFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $log = isset($options['log']) ? $options['log'] : null;

        if ($log && $container->has($log)) {
            $log = $container->get($log);
        }

        if (!$log instanceOf LoggerInterface) {
            $log = null;
        }

        $service = new LogStrategy($log);
        
        return $service;    
    }
}
