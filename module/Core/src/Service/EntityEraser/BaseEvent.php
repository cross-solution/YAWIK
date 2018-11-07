<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2018 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Service\EntityEraser;

use Zend\EventManager\Event;

/**
 * Base event for EntityEraser.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class BaseEvent extends Event
{
    /**
     * @var \Core\Repository\RepositoryService
     */
    private $repositories;

    /**
     * @return \Core\Repository\RepositoryService
     */
    public function getRepositories()
    {
        return $this->repositories;
    }

    /**
     * @param \Core\Repository\RepositoryService $repositories
     *
     * @return self
     */
    public function setRepositories($repositories)
    {
        $this->repositories = $repositories;

        return $this;
    }

    /**
     * Get a entity repository from the repository service
     *
     * @param string $name
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository|\Doctrine\ODM\MongoDB\DocumentRepository|\Core\Repository\RepositoryInterface
     */
    public function getRepository($name)
    {
        return $this->getRepositories()->get($name);
    }

    public function setParam($name, $value)
    {
        if ('repositories' == $name) {
            $this->setRepositories($value);
            return;
        }

        parent::setParam($name, $value);
    }

    public function setParams($params)
    {
        if (is_array($params) || $params instanceof \ArrayAccess) {
            if (isset($params['repositories'])) {
                $this->setRepositories($params['repositories']);
                unset($params['repositories']);
            }
        } elseif (is_object($params)) {
            if (isset($params->repositories)) {
                $this->setRepositories($params->repositories);
                unset($params->repositories);
            }
        }

        parent::setParams($params);
    }
}
