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
    /**@#+
     * {@inheritDoc}
     */
    protected $fileElement = 'Core/FileUpload';
    protected $fileName = 'attachments';
    protected $fileEntityClass = '\Applications\Entity\Attachment';
    protected $multiple = true;
    /**@#-*/
    
    /**
     * {@inheritDoc}
     * @see \Core\Form\FileUploadFactory::configureForm()
     */
    protected function configureForm($form)
    {
        $form->setLabel(/*@translate*/ 'Attachments')
             ->setIsDescriptionsEnabled(true)
             ->setDescription(/*@translate*/ 'Attach images or PDF Documents to your application. Drag&drop them, or click into the attachement area. You can upload up to 5MB')
             ->setParam('return', 'file-uri')
             ->get($this->fileName)->setMaxSize(5000000)
                                   ->setAllowedTypes(array(
                                       'image/',
                                       'application/pdf'
                                   ));
    }
}
