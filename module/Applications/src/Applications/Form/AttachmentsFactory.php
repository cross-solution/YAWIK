<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/**  */ 
namespace Applications\Form;

use Core\Form\FileUploadFactory;

/**
 * Factors a file upload form to attach files to an application.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class AttachmentsFactory extends FileUploadFactory
{
    protected $fileElement = 'Core/FileUpload';
    protected $fileName = 'attachments';
    protected $fileEntityClass = '\Applications\Entity\Attachment';
    protected $multiple = true;
    protected $configKey = 'application_attachments';

    protected function configureForm($form)
    {
        /** @var $form \Core\Form\Form */
        $form->setIsDisableCapable(false)
             ->setIsDisableElementsCapable(false)
             ->setIsDescriptionsEnabled(true)
             ->setDescription(/*@translate*/ 'Attach images or PDF Documents to your application. Drag&drop them, or click into the attachement area. You can upload up to 5MB')
             ->setParam('return', 'file-uri')
             ->setLabel(/*@translate*/ 'Attachments');

        /** @var $file \Core\Form\Element\FileUpload*/
        $file = $form->get($this->fileName);
        $size = isset($this->config['max_size']) ? $this->config['max_size'] : 5000000;
        $type = isset($this->config['mimetype']) ? $this->config['mimetype'] : null;
        $count = isset($this->config['count']) ? $this->config['count'] : 0;

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
