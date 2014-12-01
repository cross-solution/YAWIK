<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form\Hydrator;

use Core\Entity\Hydrator\EntityHydrator;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PreviewLinkHydrator extends EntityHydrator implements ServiceLocatorAwareInterface
{

    public function __construct()
    {
        parent::__construct();
        $this->init();
    }

    protected function init()
    {
    }

    /* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\HydratorInterface::extract()
     */
    public function extract ($object)
    {
        $locator = $this->getServiceLocator();
        $controllerPluginManager = $locator->get('controllerPluginManager');

        $data = parent::extract($object);
        $viewLink = $controllerPluginManager->get('url')->fromRoute(
          'lang/jobs/view',
              array(
              ),
              array(
                  'query' => array(
                      'id' => $data['id'],
                  )
              )
        );

        $data['previewLink']         = $viewLink;
        return $data;
    }

    public function hydrate (array $data, $object)
    {
        $object = parent::hydrate($data, $object);
        return $object;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}