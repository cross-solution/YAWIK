<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Cv\Options;

use Zend\Stdlib\AbstractOptions;
use Zend\ServiceManager\ServiceManager;

/**
 * Default options of the CV Module
 *
 * @author fedys
 * @since 0.26
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
    protected $attachmentsMimeType = array('image','applications/pdf',
        'application/x-pdf',
        'application/acrobat',
        'applications/vnd.pdf',
        'text/pdf',
        'text/x-pdf');

    /**
     * maximum number of attachments. Default 3
     *
     * @var int $attachmentsCount
     */
    protected $attachmentsCount = 3;

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
}