<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
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
     * HybridAuth endpoint.
     */
    public function indexAction()
    {
        \Hybrid_Endpoint::process();
    }
}

// @codeCoverageIgnoreEnd
