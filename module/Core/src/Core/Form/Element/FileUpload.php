<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/**  */ 
namespace Core\Form\Element;

use Doctrine\Common\Collections\Collection;
use Zend\Form\Element\File;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\File\MimeType;
use Zend\Validator\File\Size;
use Zend\View\Helper\HelperInterface;
use Zend\Form\FormInterface;

/**
 * File upload formular element.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class FileUpload extends File implements ViewHelperProviderInterface,
                                         InputProviderInterface
{
    /**
     * The view helper name.
     *
     * @var string
     */
    protected $helper = 'formFileUpload';

    /**
     * Is this element a multiple file upload
     *
     * @var bool
     */
    protected $isMultiple = false;

    /**
     * The form which contains this element,
     *
     * @var FormInterface
     */
    protected $form;
    
    public function setViewHelper($helper)
    {
        if (is_object($helper) && !$helper instanceOf HelperInterface) {
            throw new \InvalidArgumentException('Expects helper to be eiter a service name or an instance of "Zend\View\Helper\HelperInterface"');
        }
        
        $this->helper = $helper;
        return $this;
    }
    
    public function getViewHelper()
    {
        return $this->helper;
    }

    /**
     * Sets the maximum file size.
     *
     * @param int $bytes
     *
     * @return self
     */
    public function setMaxSize($bytes)
    {
        return $this->setAttribute('data-maxsize', $bytes);
    }

    public function getMaxSize()
    {
        return $this->getAttribute('data-maxsize');
    }

    /**
     * Sets the allowed mime types.
     *
     * The types can be passed either as an array or a comma separated list.
     *
     * @param array|string $types
     *
     * @return self
     */
    public function setAllowedTypes($types)
    {
        if (is_array($types)) {
            $types = implode(',', $types);
        }
        
        return $this->setAttribute('data-allowedtypes', $types);
    }

    public function getAllowedTypes()
    {
        $types = $this->getAttribute('data-allowedtypes');

        return $types;
    }

    /**
     * Sets if this element allows multiple files to be selected.
     *
     * @param boolean $flag
     *
     * @return self
     */
    public function setIsMultiple($flag)
    {
        $this->isMultiple = (bool) $flag;
        if ($flag) {
            $this->setAttribute('multiple', true);
        } else {
            $this->removeAttribute('multiple');
        }
        
        return $this;
    }

    /**
     * Gets if this element allows multiple files to be selected.
     *
     * @return boolean
     */
    public function isMultiple()
    {
        return $this->isMultiple;
    }

    /**
     * {@inheritDoc}
     *
     * Sets {@link form}.
     */
    public function prepareElement(FormInterface $form)
    {
        $form->setAttribute('class', ($this->isMultiple() ? 'multi' : 'single') . '-file-upload');
        $form->setAttribute('data-is-empty', null === $this->getValue());
        $this->form = $form;
        parent::prepareElement($form);
    }

    /**
     * Gets the file entity bound to the containing form,
     *
     * Returns <i>NULL</i>, if the entity cannot be retrieved or is not set in the form.
     *
     * @return null|Collection|\Core\Entity\FileInterface
     */
    public function getFileEntity()
    {
        if (!$this->form || !($object = $this->form->getObject())) {
            return;
        }
        
        if ($this->isMultiple()) {
            return $object;
        }
        
        $entityName = $this->getName();
        
        try {
            $fileEntity = $object->$entityName;
        } catch (\OutOfBoundsException $e) {
            return null;
        }
        
        return $fileEntity;
    }

    public function getInputSpecification()
    {
        $mimeTypeValidator = new MimeType();
        $mimeTypeValidator->setMagicFile(false)
                          ->disableMagicFile(true)
                          ->setMimeType($this->getAllowedTypes());

        $sizeValidator = new Size($this->getMaxSize());

        return array(
            'name' => $this->getName(),
            'required' => false,
            'validators' => array(
                $mimeTypeValidator,
                $sizeValidator,
            ),
        );
    }
    
}