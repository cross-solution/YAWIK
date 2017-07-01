<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** UniqueApplyIdFactory.php */
namespace Auth\Form\Validator;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for UniqueGroupName validator.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class UniqueGroupNameFactory implements FactoryInterface
{
    
    /**
     * Options.
     * @var array
     */
    protected $options = array();
    
    /**
     * Creates an instance.
     *
     * @param array $options
     */
    public function __construct($options)
    {
        $this->options['allowName'] = isset($options['allowName']) ? $options['allowName'] : null;
    }
	
	public function __invoke( ContainerInterface $container, $requestedName, array $options = null )
	{
		$auth      = $container->get('AuthenticationService');
		$user      = $auth->getUser();
		$options   = $this->options;
		$options['user'] = $user;
		$validator = new UniqueGroupName($options);
		
		return $validator;
	}
}
