<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** ImageController.php */ 
namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class ImageController extends AbstractActionController
{
    
    public function indexAction()
    {
        $fileId = $this->params('id', 0);
        return $this->fileSender('user-file', $fileId);
    }
}

