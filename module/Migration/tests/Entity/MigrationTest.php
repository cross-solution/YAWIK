<?php

declare(strict_types=1);

namespace Yawik\Migration\Tests\Entity;

use Yawik\Migration\Entity\Migration;
use CoreTestUtils\TestCase\FunctionalTestCase;
use Yawik\Migration\Tests\TestMigrator;

/**
 * @covers \Yawik\Migration\Entity\Migration
 */
class MigrationTest extends FunctionalTestCase
{
    private function create()
    {
        return new Migration(
            TestMigrator::class,
            'version',
            'test',
        );
    }

    public function testCreate()
    {
        $migration = $this->create();
        $this->assertEquals(TestMigrator::class, $migration->getClass());
        $this->assertEquals('version', $migration->getVersion());
        $this->assertEquals('test', $migration->getDescription());
        $this->assertNull($migration->getMigratedAt());
        $this->assertFalse($migration->isMigrated());

        $migration->setMigrated(true);
        $this->assertTrue($migration->isMigrated());
    }
}