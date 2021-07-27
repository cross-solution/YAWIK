<?php

declare(strict_types=1);

namespace Yawik\Migration\Tests\Handler;

use CoreTestUtils\TestCase\FunctionalTestCase;
use InvalidArgumentException;
use Yawik\Migration\Handler\MigrationHandler;
use Yawik\Migration\Tests\TestMigrator;

/**
 * @covers \Yawik\Migration\Handler\MigrationHandler
 */
class MigrationHandlerTest extends FunctionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $handler = $this->getHandler();
        $ob = $handler->findOrCreate(new TestMigrator(), false);
        $dm = $this->getDoctrine();

        if(!is_null($ob)){
            $dm->remove($ob);
            $dm->flush();
        }
    }

    /**
     * @return MigrationHandler
     */
    private function getHandler()
    {
        return $this->getService(MigrationHandler::class);
    }

    private function create($className = TestMigrator::class)
    {
        $handler = $this->getHandler();
        $migrator = new $className();

        return $handler->findOrCreate($migrator, true);
    }

    public function testFindOrCreate()
    {
        $ob = $this->getHandler()->findOrCreate(new TestMigrator(), true);
        $this->assertNotNull($ob);
    }

    public function testCreate()
    {
        $ob = $this->create();

        $this->assertNotNull($ob);
        $this->assertNotNull($ob->getId());
    }

    public function testMigrated()
    {
        $ob = $this->create();
        $handler = $this->getHandler();

        $this->assertNull($ob->getMigratedAt());
        $this->assertFalse($ob->isMigrated());
        $handler->migrated(new TestMigrator());

        $this->assertTrue($ob->isMigrated());
        $this->assertNotNull($ob->getMigratedAt());
    }

    public function testMigratedThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $handler = $this->getHandler();
        $handler->migrated(new TestMigrator());
    }
}