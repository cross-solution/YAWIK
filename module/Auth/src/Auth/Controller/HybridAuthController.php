<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;

/**
 * Controller to handle HybridAuth logins.
 *
 */
class HybridAuthController extends AbstractActionController
{
    public function indexAction()
    {
        \Hybrid_Endpoint::process();
    }
}
