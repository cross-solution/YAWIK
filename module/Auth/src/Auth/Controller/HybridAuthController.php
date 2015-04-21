<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Auth controller */
namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;

// @codeCoverageIgnoreStart

/**
 * Controller for the HybridAuth endpoint.
 *
 */
class HybridAuthController extends AbstractActionController
{
    /**
     * attaches further Listeners for generating / processing the output
     * @return $this
     */
    public function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $serviceLocator  = $this->getServiceLocator();
        $defaultServices = $serviceLocator->get('DefaultListeners');
        $events          = $this->getEventManager();
        $events->attach($defaultServices);
        return $this;
    }

    /**
     * HybridAuth endpoint.
     */
    public function indexAction()
    {
        \Hybrid_Endpoint::process();
    }
}

// @codeCoverageIgnoreEnd
