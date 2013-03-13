<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
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
