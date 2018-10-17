<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\Controller;

use Auth\Controller\Plugin\Auth;
use Core\Controller\Plugin\Notification;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Class AbstractCoreController
 *
 * @method Notification notification()
 * @method Auth auth()
 *
 * @package Core\Controller
 */
abstract class AbstractCoreController extends AbstractActionController
{
}
