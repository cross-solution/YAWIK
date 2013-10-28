<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** FileUploadHydrator.php */ 
namespace Applications\Repository\Hydrator;

use Core\Repository\Hydrator\FileUploadHydrator as CoreFileUploadHydrator;
use Zend\Form\FormInterface;
use Applications\Entity\ApplicationInterface;
use Zend\Authentication\AuthenticationService;

class FileUploadHydrator extends CoreFileUploadHydrator
{
    
    protected $form;
    protected $auth;
    
    public function setForm(FormInterface $form)
    {
        $this->form = $form;
        return $this;
    }
    
    public function setAuth(AuthenticationService $auth)
    {
        $this->auth = $auth;
        return $this;
    }

    public function hydrate (array $data, $object)
    {
        $ids = array($this->form->getObject()->job->userId);
        if ($this->auth->hasIdentity()) {
            $ids[] = $this->auth->getIdentity();
        }
        $data['meta']['allowedUserIds'] = $ids;
        return parent::hydrate($data, $object);
    }
}

