<?php

declare(strict_types=1);

namespace Yawik\Migration\Controller;


use Laminas\Mvc\Console\Controller\AbstractConsoleController;
use Psr\Container\ContainerInterface;
use Yawik\Migration\Contracts\MigratorInterface;
use Yawik\Migration\Migrator\Version36;

class ConsoleController extends AbstractConsoleController
{
    /**
     * @var iterable|MigratorInterface[]
     */
    private iterable $migrators;

    /**
     * MigrationController constructor.
     *
     * @param iterable $migrators
     */
    public function __construct(
        iterable $migrators
    )
    {
        $this->migrators = $migrators;
    }

    public static function factory(ContainerInterface $container)
    {
        $migrators = [];
        $migrators[] = $container->get(Version36::class);

        return new self($migrators);
    }

    public function migrateAction()
    {
        foreach($this->migrators as $migrator){
            $migrator->migrate();
        }
    }

}