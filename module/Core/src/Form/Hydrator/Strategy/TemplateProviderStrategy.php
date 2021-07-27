<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Form\Hydrator\Strategy;

use Laminas\Hydrator\Strategy\StrategyInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class TemplateProviderStrategy implements StrategyInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceManager;
    
    /**
     * @param ServiceLocatorInterface $serviceManager
     */
    public function __construct(ServiceLocatorInterface $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    public function extract($value, ?object $object = null)
    {
        $templateProvider = $this->serviceManager->get('templateProvider');
        $templateProvider->setValue($value, $object);
        return $templateProvider;
    }

    public function hydrate($value, ?array $data)
    {
    }
    
    /**
     * @param ServiceLocatorInterface $serviceManager
     * @return \Core\Form\Hydrator\Strategy\TemplateProviderStrategy
     */
    public static function factory(ServiceLocatorInterface $serviceManager)
    {
        return new static($serviceManager);
    }
}
