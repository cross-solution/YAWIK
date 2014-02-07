<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** FileCollection.php */ 
namespace Core\Form;

use Zend\Form\Element\Collection;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\ValidatorInterface;
use Core\Entity\FileInterface;
use Core\Entity\FileEntity;
use Core\Entity\Collection\ArrayCollection;

class FileCollection extends Collection implements InputFilterProviderInterface
{
   
    protected $fileEntity;
    protected $fileValidator;
    
    public function setFileEntity(FileInterface $file)
    {
        $this->fileEntity = $file;
        return $this;
    }
    
    public function getFileEntity()
    {
        if (!$this->fileEntity) {
            $this->setFileEntity(new FileEntity());
        }
        return clone $this->fileEntity;
    }
    
    public function bindValues(array $values = array())
    {
        $hydrator = $this->getHydrator();
        $collection = new ArrayCollection();
        
        foreach ($values as $name => $value) {
            if (UPLOAD_ERR_OK != $value['error']) {
                continue;
            }
            $element = $this->get($name);
    
            $entity = $hydrator->hydrate($value, $this->getFileEntity());
            $collection->add($entity);
        }
    
        return $collection;
    }
    
    public function setFileValidator(ValidatorInterface $validator)
    {
        $this->fileValidator = $validator;
        return $this;
    }
    
    public function getFileValidator()
    {
        return $this->fileValidator;
    }
     
    /* (non-PHPdoc)
     * @see \Zend\InputFilter\InputFilterProviderInterface::getInputFilterSpecification()
     */
    public function getInputFilterSpecification() {
        $input = array(
            'type' => 'Zend\InputFilter\FileInput',
            'required' => false,
        );
        /*if ($validator = $this->getFileValidator()) {
            $input['validators'] = array($validator);
        }*///Removed file validator due to possible bug.
        $spec = array();
        foreach ($this->getElements() as $element) {
            /*
             * We need to explicitally set the element name due to code
             * in Zend\InputFilter\BaseInputFilter::add.
             * When element name is an integer, the input->getName() method is called
             * to determine the name. And numeric array keys are automatically 
             * transformed to integers. As this is a Collection of File-Inputs,
             * the element names ARE numeric!
             */
            $name = (string) $element->getName();
            $spec[$name] = $input + array('name' => $name);
        }

        return $spec;
    }

}

