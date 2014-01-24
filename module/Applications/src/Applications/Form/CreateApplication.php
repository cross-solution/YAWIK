<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Applications\Form;

use Core\Entity\Hydrator\EntityHydrator;
use Applications\Entity\Attachment;
use Applications\Entity\Cv;
use Core\Form\Form;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * create an application form.
 */
class CreateApplication extends Form
{
    protected $forms;
    protected $inputFilterSpecification;
    protected $preferFormInputFilter = true;
    protected $isInitialized;
    
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->forms = $serviceLocator;
        return $this;
    }
    
    public function getServiceLocator()
    {
        return $this->forms;
    }
    
    /*
     * hydrating strategies are defined by doctrine annotations
     */
    public function getHydrator()
    {
        if (!$this->hydrator) {
             $hydrator = new EntityHydrator();
        }
        return $this->hydrator;
    }
    
    public function setObject($object)
    {
        parent::setObject($object);
        if (!$this->isInitialized) {
            $this->initLazy();
            $this->isInitialized = true;
        }
        $this->get('base')->setObject($object);
        return $this;
    }
    
	public function initLazy()
    {
        $this->setName('create-application-form');
        
        $this->add(array(
            'type' => 'hidden',
            'name' => 'jobId',
            'required' => true
        ));
        
        /**
         * @todo: das versteht kein Mensch
         */
   
        $this->add($this->forms
                         ->get('Applications/ContactFieldset', array(
                                'image_meta' => array(
                                    'allowedUserIds' => array(
                                        $this->getObject()->getJob()->userId
                                    )
                                )
                           ))
                         ->setLabel('personal informations')
                         ->setName('contact')
                         ->setObject(new Attachment()));
        
        
        $this->add($this->forms->get('Applications/BaseFieldset'));

        /**
         * ads a cv section to the application formular
         */
        
        $this->add(
            $this->forms->get('CvFieldset')->setObject(new Cv())
        );
        
        $attachments = $this->forms->get('Applications/AttachmentsCollection');
        $attachments->getHydrator()->setForm($this); 
        $this->add(
            $attachments
        );
        
        /**
         * ads the privacy policy to the application fomular
         */
        $this->add(
            $this->forms->get('Applications/Privacy')
        );

        $this->add($this->forms->get('DefaultButtonsFieldset'));
        //$this->setValidationGroup('jobId', 'contact', 'base', 'cv');
       
    }
    
}