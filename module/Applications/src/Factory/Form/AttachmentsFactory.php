<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/**  */
namespace Applications\Factory\Form;

use Core\Form\FileUploadFactory;
use Core\Form\Element\FileUpload;
use Core\Form\Form;
use Zend\Stdlib\AbstractOptions;
use Applications\Options\ModuleOptions;

/**
 * Factors a file upload form to attach files to an application.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class AttachmentsFactory extends FileUploadFactory
{
    /**
     * Form element for the file upload
     *
     * @var string
     */
    protected $fileElement = 'Core/FileUpload';

    /**
     * Name of the file, if downloaded.
     *
     * @var string
     */
    protected $fileName = 'attachments';

    /**
     * Entity for storing the attachment
     *
     * @var string
     */
    protected $fileEntityClass = '\Applications\Entity\Attachment';

    /**
     * allow to upload multiple files
     *
     * @var bool
     */
    protected $multiple = true;

    /**
     * use abstract options defined in "Applications/Options"
     *
     * @var string
     */
    protected $options="Applications/Options";


    /**
     * configure the formular for uploading attachments
     *
     * @param Form $form
     * @param AbstractOptions $options
     */
    protected function configureForm($form, AbstractOptions $options)
    {
        /** @var ModuleOptions $options */
        $size = $options->getAttachmentsMaxSize();
        $type = $options->getAttachmentsMimeType();
        $count = $options->getAttachmentsCount();

        $form->setIsDisableCapable(false)
             ->setIsDisableElementsCapable(false)
             ->setIsDescriptionsEnabled(true)
             ->setDescription(
                /*@translate*/ 'Attach images or PDF Documents to your application. Drag&drop them, or click into the attachement area. You can upload up to %sMB',
                 [round($size/(1024*1024))>0? round($size/(1024*1024)):round($size/(1024*1024), 1)]
             )
             ->setParam('return', 'file-uri')
             ->setLabel(/*@translate*/ 'Attachments');

        /** @var $file FileUpload*/
        $file = $form->get($this->fileName);

        $file->setMaxSize($size);
        if ($type) {
            $file->setAllowedTypes($type);
        }
        $file->setMaxFileCount($count);

        // pass form to element. Needed for file count validation
        // I did not find another (better) way.
        $file->setForm($form);
    }
}
