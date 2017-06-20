<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** UniqueApplyIdFactory.php */
namespace Jobs\Form\Validator;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class UniqueApplyIdFactory implements FactoryInterface
{
	public function __invoke( ContainerInterface $container, $requestedName, array $options = null )
	{
		$repositories = $container->get('repositories');
		$jobs         = $repositories->get('Jobs/Job');
		$validator    = new UniqueApplyId($jobs);
		
		return $validator;
	}
}
