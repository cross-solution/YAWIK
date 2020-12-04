<?php

declare(strict_types=1);

namespace Yawik\Migration\Handler;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Persistence\ObjectRepository;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Yawik\Migration\Contracts\MigratorInterface;
use Yawik\Migration\Entity\Migration;

class MigrationHandler
{
    private DocumentManager $dm;

    private ObjectRepository $repo;

    public function __construct(
        DocumentManager $dm
    )
    {
        $this->dm = $dm;
        $this->repo = $dm->getRepository(Migration::class);
    }

    public static function factory(ContainerInterface $container)
    {
        $dm = $container->get(DocumentManager::class);

        return new self($dm);
    }

    /**
     * @param MigratorInterface $migrator
     * @param bool $create
     * @return null|object|Migration
     */
    public function findOrCreate(MigratorInterface $migrator, bool $create = false)
    {
        $ob =  $this->repo->findOneBy([
            'class' => get_class($migrator)
        ]);

        if(is_null($ob) && $create){
            $ob = $this->create($migrator);
        }

        return $ob;
    }

    public function create(MigratorInterface $migrator): Migration
    {
        $dm = $this->dm;
        $ob = new Migration(
            get_class($migrator),
            $migrator->version(),
            $migrator->getDescription(),
        );

        $dm->persist($ob);
        $dm->flush();

        return $ob;
    }

    public function migrated(MigratorInterface $migrator): Migration
    {
        $migration = $this->findOrCreate($migrator);
        if(!is_null($migration)){
            $migration->setMigrated(true);
            $migration->setMigratedAt(new \DateTime());
            return $migration;
        }
        throw new InvalidArgumentException(sprintf(
            "Migration data for '%s' is not exists in database.",
            get_class($migrator)
        ));
    }
}