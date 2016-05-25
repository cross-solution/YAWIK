<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Applications\Entity;

use Core\Entity\FileEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * An Attachment of an application
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 * @ODM\Document(collection="applications")
 */
class Attachment extends FileEntity
{

    /**
     * Gets the URI of an attachment
     *
     * The returned URI is NOT prepended with the base path!
     *
     * @return string
     */
    public function getUri()
    {
        return "/file/Applications.Attachment/" . $this->id . "/" .urlencode($this->name);
    }
}
