<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** FileController.php */ 
namespace Applications\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class FileController extends AbstractActionController
{

    public function indexAction()
    {
        $fileId = $this->params('id', 0);
        return $this->fileSender('Applications/FileRepository', $fileId);
    }
}


