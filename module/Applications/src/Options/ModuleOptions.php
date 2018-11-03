<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @license   MIT
 */

namespace Applications\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class ModuleOptions
 *
 * Default options of the Applications Module
 *
 * @package Applications\Options
 */
class ModuleOptions extends AbstractOptions
{

    /**
     * maximum size in bytes of an attachment. Default 5MB
     *
     * @var int $attachmentsMaxSize
     */
    protected $attachmentsMaxSize = 5000000;

    /**
     * valid Mime-Types of attachments
     *
     * @var array $attachmentsMimeType
     */
    protected $attachmentsMimeType = array(
        'image',
        'application/pdf',
        'applications/pdf',
        'application/x-pdf',
        'application/acrobat',
        'applications/vnd.pdf',
        'text/pdf',
        'text/x-pdf'
    );

    /**
     * maximum number of attachments. Default 3
     *
     * @var int $attachmentsCount
     */
    protected $attachmentsCount = 3;

    /**
     * maximum size of an user image. Default 200 kB
     *
     * @var int $contactImageMaxSize
     */
    protected $contactImageMaxSize = 200000;

    /**
     * allowed Mime-Type of a user image
     *
     * @var array $contactImageMimeType
     */
    protected $contactImageMimeType = array('image');

    /**
     * Generally allowed Mime Types
     *
     * @var array $allowedMimeTypes
     */
    protected $allowedMimeTypes = array('image',
                                        'applications/pdf',
                                        'application/x-pdf',
                                        'application/acrobat',
                                        'applications/vnd.pdf',
                                        'text/pdf',
                                        'text/x-pdf',
                                        'text');


    protected $workflow = [

      'recruiter',
    ];
    
    /**
     * Flag indicating whether subsequent attachment uploads are allowed
     *
     * @var bool
     */
    protected $allowSubsequentAttachmentUpload = false;

    /**
     * Gets the maximum size of attachments in bytes
     *
     * @return int
     */
    public function getAttachmentsMaxSize()
    {
        return $this->attachmentsMaxSize;
    }
    /**
     * Sets the maximum size of attachments in bytes
     *
     * @param int $size
     * @return ModuleOptions
     */
    public function setAttachmentsMaxSize($size)
    {
        $this->attachmentsMaxSize = $size;
        return $this;
    }

    /**
     * Gets the the allowed Mime-Types for attachments
     *
     * @return array
     */
    public function getAttachmentsMimeType()
    {
        return $this->attachmentsMimeType;
    }
    /**
     * Sets the maximum size of attachments in bytes
     *
     * @param array $mime
     * @return ModuleOptions
     */
    public function setAttachmentsMimeType(array $mime)
    {
        $this->attachmentsMimeType = $mime;
        return $this;
    }

    /**
     * Gets the the maximum number of allowed attachments
     *
     * @return string
     */
    public function getAttachmentsCount()
    {
        return $this->attachmentsCount;
    }
    /**
     * Sets the maximum number of allowed attachments
     *
     * @param string $number
     * @return ModuleOptions
     */
    public function setAttachmentsCount($number)
    {
        $this->attachmentsCount = $number;
        return $this;
    }

    /**
     * Gets the the maximum size of contact images in bytes
     *
     * @return string
     */
    public function getContactImageMaxSize()
    {
        return $this->contactImageMaxSize;
    }
    /**
     * Sets the maximum size of contact images in bytes
     *
     * @param string $size
     * @return ModuleOptions
     */
    public function setContactImageMaxSize($size)
    {
        $this->contactImageMaxSize = $size;
        return $this;
    }

    /**
     * Gets the allowed Mime-Types for contact images
     *
     * @return array
     */
    public function getContactImageMimeType()
    {
        return $this->contactImageMimeType;
    }
    /**
     * Sets the allowed Mime-Types for contact images
     *
     * @param array $mime
     * @return ModuleOptions
     */
    public function setContactImageMimeType($mime)
    {
        $this->contactImageMimeType = $mime;
        return $this;
    }

    /**
     * Gets the allowed Mime-Types for contact images
     *
     * @return string
     */
    public function getAllowedMimeTypes()
    {
        return $this->allowedMimeTypes;
    }

    /**
     * Sets the allowed Mime-Types
     *
     * @param array $array
     * @return ModuleOptions
     */
    public function setAllowedMimeTypes($array)
    {
        $this->allowedMimeTypes = $array;
        return $this;
    }
    
    /**
     * @return boolean
     * @since 0.27
     */
    public function getAllowSubsequentAttachmentUpload()
    {
        return $this->allowSubsequentAttachmentUpload;
    }

    /**
     * @param boolean $allowSubsequentAttachmentUpload
     * @return ModuleOptions
     * @since 0.27
     */
    public function setAllowSubsequentAttachmentUpload($allowSubsequentAttachmentUpload)
    {
        $this->allowSubsequentAttachmentUpload = (bool)$allowSubsequentAttachmentUpload;
        
        return $this;
    }
}
