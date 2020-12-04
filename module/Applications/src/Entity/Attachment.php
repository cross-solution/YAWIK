<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Applications\Entity;

use Core\Entity\File;
use Core\Entity\FileInterface;
use Core\Entity\FileMetadata;
use Core\Entity\FileTrait;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * An Attachment of an application
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 * @ODM\File(bucketName="applications")
 */
class Attachment extends File
{
    /**
     * Gets the URI of an attachment
     *
     * The returned URI is NOT prepended with the base path!
     *
     * @return string
     */
    public function getUri(): string
    {
        return "/file/Applications.Attachment/" . $this->id . "/" .urlencode($this->name);
    }
}
