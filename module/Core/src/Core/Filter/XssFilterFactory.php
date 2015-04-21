<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** XssFilterFactory.php */
namespace Core\Filter;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use zf2htmlpurifier\Filter;


/**
 * Factory for the XssFilter
 *
 * @author Cristian Stinga <gelhausen@cross-solution.de>
 */
class XssFilterFactory implements FactoryInterface
{

    /**
     * Creates xss filter Service
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     * @param ServiceLocatorInterface $serviceLocator
     * @return XssFilter|mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator){

       $htmlPurifier = new Filter\HTMLPurifierFilter();

       $filter = new XssFilter($htmlPurifier);

       return $filter;
    }
}