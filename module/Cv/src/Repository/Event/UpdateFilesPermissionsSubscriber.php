<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/**  */
namespace Cv\Repository\Event;


use Core\Repository\DoctrineMongoODM\Event\AbstractUpdateFilesPermissionsSubscriber;
use Cv\Entity\Cv;

/**
 * class for updating file permissions
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.26
 */
class UpdateFilesPermissionsSubscriber extends AbstractUpdateFilesPermissionsSubscriber
{
    protected $targetDocument = Cv::class;

    protected $filesProperties = [ 'attachments' ];

    /**
     *
     *
     * @param \Cv\Entity\Cv $document
     *
     * @return array|\Core\Entity\PermissionsAwareInterface[]
     */
    protected function getFiles($document)
    {
        $files = parent::getFiles($document);

        if ($document->notEmpty('contact') && ($image = $document->getContact()->getImage())) {
            $files[] = $image;
        }

        return $files;
    }
}
