<?php

declare(strict_types=1);

namespace Yawik\Migration\Controller;


use Laminas\Mvc\Console\Controller\AbstractConsoleController;
use Psr\Container\ContainerInterface;
use Yawik\Migration\Contracts\MigratorInterface;
use Yawik\Migration\Handler\MigrationHandler;
use Yawik\Migration\Migrator\Version36;

class ConsoleController extends AbstractConsoleController
{
    /**
     * @var iterable|MigratorInterface[]
     */
    private iterable $migrators;
    /**
     * @var MigrationHandler
     */
    private MigrationHandler $handler;

    /**
     * MigrationController constructor.
     *
     * @param MigrationHandler $handler
     * @param iterable $migrators
     */
    public function __construct(
        MigrationHandler $handler,
        iterable $migrators
    )
    {
        $this->migrators = $migrators;
        $this->handler = $handler;
    }

    public static function factory(ContainerInterface $container)
    {
        $handler = $container->get(MigrationHandler::class);
        $migrators = [];
        $migrators[] = $container->get(Version36::class);

        return new self($handler, $migrators);
    }

    public function migrateAction()
    {
        $handler = $this->handler;
        foreach($this->migrators as $migrator){
            $status = $handler->findOrCreate($migrator, true);
            if(!$status->isMigrated()){
                $success = $migrator->migrate();
                if($success){
                    $handler->migrated($migrator);
                }
            }
        }
    }

}