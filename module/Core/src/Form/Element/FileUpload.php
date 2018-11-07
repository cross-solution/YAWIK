<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

/**  */
namespace Core\Form\Element;

use Doctrine\Common\Collections\Collection;
use Zend\Form\Element\File;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\Callback;
use Zend\Validator\File\MimeType;
use Zend\Validator\File\Size;
use Zend\View\Helper\HelperInterface;
use Zend\Form\FormInterface;

/**
 * File upload formular element.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class FileUpload extends File implements
    ViewHelperProviderInterface,
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
     * The form which contains this element
     *
     * @var FormInterface
     */
    protected $form;

    public function setViewHelper($helper)
    {
        if (is_object($helper) && !$helper instanceof HelperInterface) {
            throw new \InvalidArgumentException('Expects helper to be either a service name or an instance of "Zend\View\Helper\HelperInterface"');
        }

        $this->helper = $helper;

        return $this;
    }

    public function getViewHelper()
    {
        return $this->helper;
    }

    /**
     * Sets the form instance of the form which contains this element.
     * This instance is needed in
     * {@link fileCountValidationCallback()} and
     * {@link getFileEntity()}
     *
     * @param \Zend\Form\FormInterface $form
     *
     * @return self
     */
    public function setForm(FormInterface $form)
    {
        $this->form = $form;

        return $this;
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

    /**
     * Sets the allowed mime types.
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

    /**
     * Sets the maximum files count.
     *
     * @param int $count
     *
     * @return self
     */
    public function setMaxFileCount($count)
    {
        return $this->setAttribute('data-maxfilecount', (int) $count);
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

    public function prepareElement(FormInterface $form)
    {
        $form->setAttribute('class', ($this->isMultiple() ? 'multi' : 'single') . '-file-upload');
        $form->setAttribute('data-is-empty', null === $this->getValue());
        parent::prepareElement($form);
    }

    public function getInputSpecification()
    {
        $validators = array();
        $mimetypes  = $this->getAllowedTypes();
        $fileCount  = $this->getMaxFileCount();

        if ($mimetypes) {
            $mimeTypeValidator = new MimeType();
            $mimeTypeValidator->setMagicFile(false)
                              ->disableMagicFile(true)
                              ->setMimeType($this->getAllowedTypes());

            $validators[] = $mimeTypeValidator;
        }

        $validators[] = new Size($this->getMaxSize());

        if (0 < $fileCount) {
            $validators[] = new Callback(array($this, 'fileCountValidationCallback'));
        }

        return array(
            'name'       => $this->getName(),
            'required'   => false,
            'validators' => $validators,
        );
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
     * Gets the allowed mimetypes
     *
     * @param bool $asArray if true, the types are returned as an array,
     *                      if false, the types are returned as comma separated string.
     *
     * @return string|array
     */
    public function getAllowedTypes($asArray = false)
    {
        $types = $this->getAttribute('data-allowedtypes');

        if ($asArray) {
            return explode(',', $types);
        }

        return $types;
    }

    /**
     * Gets the maximum files count.
     *
     * @return int
     */
    public function getMaxFileCount()
    {
        $count = $this->getAttribute('data-maxfilecount');

        return $count;
    }

    /**
     * Gets the maximum file size
     *
     * @return int
     */
    public function getMaxSize()
    {
        return $this->getAttribute('data-maxsize');
    }

    /**
     * Gets the file entity bound to the containing form,
     * Returns <i>NULL</i>, if the entity cannot be retrieved or is not set in the form.
     *
     * @return null|Collection|\Core\Entity\FileInterface
     */
    public function getFileEntity()
    {
        if (!$this->form || !($object = $this->form->getObject())) {
            return null;
        }

        if ($this->isMultiple()) {
            return $object;
        }

        $entityName = $this->getName();

        try {
            $fileEntity = $object->{"get" . $entityName}();
        } catch (\OutOfBoundsException $e) {
            return null;
        }

        return $fileEntity;
    }

    /**
     * Callback for file count validation.
     *
     * @internal
     *      This function gets the value passed in as variable,
     *      but we do not need it.
     * @return bool
     */
    public function fileCountValidationCallback()
    {
        if ($this->form && ($object = $this->form->getObject())) {
            if ($this->getMaxFileCount() - 1 < count($object)) {
                return false;
            }
        }

        return true;
    }
}
