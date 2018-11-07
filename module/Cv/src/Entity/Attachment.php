<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

namespace Cv\Entity;

use Core\Entity\FileEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * An Attachment of a CV
 *
 * @author fedys
 * @since 0.26
 *
 * @ODM\Document(collection="cvs.attachments")
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
        return "/file/Cv.Attachment/" . $this->getId() . "/" .urlencode($this->name);
    }
}
