<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Listener\Events;

use ArrayAccess;
use Core\Entity\FileEntity;
use Zend\EventManager\Event;
use Zend\EventManager\Exception;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class FileEvent extends Event
{
    const EVENT_DELETE = 'delete';

    /**
     *
     *
     * @var FileEntity
     */
    private $file;

    public function setParams($params)
    {
        if (is_array($params)) {
            if (isset($params['file'])) {
                $this->setFile($params['file']);
                unset($params['file']);
            }
        }

        return parent::setParams($params);
    }

    /**
     * @param \Core\Entity\FileEntity $file
     *
     * @return self
     */
    public function setFile(FileEntity $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return \Core\Entity\FileEntity
     */
    public function getFile()
    {
        return $this->file ?: $this->getParam('file');
    }
}