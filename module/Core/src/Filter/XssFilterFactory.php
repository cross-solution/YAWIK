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

use Core\Options\ModuleOptions;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Core\Bridge\HtmlPurifier\HTMLPurifierFilter;

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
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var \Core\Options\ModuleOptions $options */
        $options = $container->get(ModuleOptions::class);
        $cacheDir = $options->getCacheDir();

        if(!is_dir($cacheDir)){
            mkdir($cacheDir,0777, true);
        }

        $htmlPurifier = new HTMLPurifierFilter();
        $htmlPurifier->setConfig(array("Cache.SerializerPath" => $cacheDir));

        $filter = new XssFilter($htmlPurifier);
        
        return $filter;
    }
}
