<?php

declare(strict_types=1);

namespace Yawik\Migration\Migrator;


use Auth\Entity\User;
use Auth\Service\UploadHandler;
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\Database;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yawik\Migration\Contracts\ProcessorInterface;
use Yawik\Migration\Contracts\MigratorInterface;
use Yawik\Migration\Migrator\Version36\OrganizationProcessor;
use Yawik\Migration\Migrator\Version36\FileProcessor;

/**
 * Migrate old yawik database to 0.36
 *
 * @package Yawik\Migration
 */
class Version36 implements MigratorInterface
{
    /**
     * @var DocumentManager
     */
    private DocumentManager $dm;

    private Database $db;

    /**
     * @var OutputInterface
     */
    private OutputInterface $output;

    /**
     * @var iterable|ProcessorInterface[]
     */
    private iterable $processors;

    public function __construct(
        DocumentManager $dm,
        OutputInterface $output
    )
    {
        $this->dm = $dm;
        $this->db = $dm->getDocumentDatabase(User::class);
        $this->output = $output;
    }

    public static function factory(ContainerInterface $container)
    {
        $dm = $container->get(DocumentManager::class);
        $output = $container->get(OutputInterface::class);

        $migrator = new self($dm, $output);
        $migrator
            ->createFileProcessor(
            'applications'
            )
            ->createFileProcessor(
            'cvs.attachments',
            )
            ->createFileProcessor(
                'cvs.contact.images',
            )
            ->createFileProcessor(
                'organizations.images'
            )
            ->createFileProcessor(
                'users.images',
            )
        ;
        $migrator->addProcessor(new OrganizationProcessor($dm, $output));
        return $migrator;
    }

    public function createFileProcessor(
        string $bucketName
    )
    {
        $this->addProcessor(new FileProcessor(
            $this->dm,
            $this->output,
            $bucketName,
        ));

        return $this;
    }

    public function addProcessor(ProcessorInterface $processor)
    {
        $this->processors[] = $processor;
    }

    public function getDescription(): string
    {
        return "Migrate Older Yawik (version<=0.36)";
    }

    public function version(): string
    {
        return "0.36.0";
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function migrate(): bool
    {
        $status = true;
        foreach($this->processors as $processor){
            try{
                $cStat = $processor->process();
            }catch (\Exception $exception){
                $cStat = false;
            }
            if(false === $cStat){
                $status = false;
            }
        }
        return $status;
    }
}