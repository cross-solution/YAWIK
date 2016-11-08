<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 */
namespace Core\Repository\DoctrineMongoODM;

use Doctrine\ODM\MongoDB\PersistentCollection\AbstractPersistentCollectionFactory;
use Core\Repository\RepositoryService;
use Core\Entity\Collection\AttachedEntitiesCollection;

class CollectionFactory extends AbstractPersistentCollectionFactory
{
    
    /**
     * @var callable
     */
    protected $repositoriesFactory;
    
    /**
     * @var RepositoryService
     */
    protected $repositories;
    
    /**
     * @param RepositoryService $repositories
     */
    public function __construct(callable $repositoriesFactory)
    {
        $this->repositoriesFactory = $repositoriesFactory;
    }

    /**
     * {@inheritDoc}
     * @see \Doctrine\ODM\MongoDB\PersistentCollection\AbstractPersistentCollectionFactory::createCollectionClass()
     */
    protected function createCollectionClass($collectionClass)
    {
        switch ($collectionClass) {
            case AttachedEntitiesCollection::class:
                if (!isset($this->repositories)) {
                    $repositoriesFactory = $this->repositoriesFactory;
                    $this->repositories = $repositoriesFactory();
                }
                return new $collectionClass($this->repositories);
            default:
                return new $collectionClass();
        }
    }
}
