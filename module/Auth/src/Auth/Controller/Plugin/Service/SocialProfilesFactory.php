<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** SocialProfilesFactory.php */
namespace Auth\Controller\Plugin\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Auth\Controller\Plugin\SocialProfiles;

class SocialProfilesFactory implements FactoryInterface
{
	public function __invoke( ContainerInterface $container, $requestedName, array $options = null )
	{
		$request = $container->get('request');
		$hybridAuth = $container->get('HybridAuth');
		$plugin     = new SocialProfiles($hybridAuth,$request);
		
		return $plugin;
	}
}
