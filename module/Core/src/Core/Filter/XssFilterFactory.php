<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** XssFilterFactory.php */
namespace Core\Filter;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use zf2htmlpurifier\Filter;

/**
 * Factory for the XssFilter
 *
 * @author Cristian Stinga <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 */
class XssFilterFactory implements FactoryInterface
{
	/**
	 * @param ContainerInterface $container
	 * @param string $requestedName
	 * @param array|null $options
	 *
	 * @return XssFilter
	 */
	public function __invoke( ContainerInterface $container, $requestedName, array $options = null )
	{
		$htmlPurifier = new Filter\HTMLPurifierFilter();
		$htmlPurifier->setConfig(array("Cache.SerializerPath" => "cache/HtmlPurifier"));
		
		$filter = new XssFilter($htmlPurifier);
		
		return $filter;
	}
}
