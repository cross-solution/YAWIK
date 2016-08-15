<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form\Hydrator;

use Core\Entity\Hydrator\EntityHydrator;
use Zend\ServiceManager\ServiceLocatorInterface;

class PreviewLinkHydrator extends EntityHydrator
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
        parent::__construct();
        $this->serviceManager = $serviceManager;
        $this->init();
    }

    protected function init()
    {
    }

    /* (non-PHPdoc)
     * @see \Zend\Hydrator\HydratorInterface::extract()
     */
    public function extract($object)
    {
        $controllerPluginManager = $this->serviceManager->get('controllerPluginManager');

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

    public function hydrate(array $data, $object)
    {
        $object = parent::hydrate($data, $object);
        return $object;
    }
    
    /**
     * @param ServiceLocatorInterface $serviceManager
     * @return \Jobs\Form\Hydrator\PreviewLinkHydrator
     */
    public static function factory(ServiceLocatorInterface $serviceManager)
    {
        return new static($serviceManager);
    }
}
