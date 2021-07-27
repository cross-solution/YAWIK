<?php

declare(strict_types=1);

namespace Yawik\Migration\Tests\Migrator\Version36;

use CoreTestUtils\TestCase\FunctionalTestCase;
use Symfony\Component\Console\Output\StreamOutput;
use Yawik\Migration\Migrator\Version36\FileProcessor;
use Yawik\Migration\Tests\DatabaseConcernTrait;

/**
 * Class FileProcessorTest
 *
 * @covers \Yawik\Migration\Migrator\Version36\FileProcessor
 * @package Yawik\Migration\Tests\Migrator\Version36
 */
class FileProcessorTest extends FunctionalTestCase
{
    use DatabaseConcernTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->initialize($this->getApplicationServiceLocator());
    }

    private function createData()
    {
        $bucket = $this->getBucket('test.files');
        $this->drop('test');
        $bucket->drop();


        $this->createFile('test');
        $this->createFile('test');
        $this->createFile("test");
        $this->createFile('test');
    }

    private function createProcessor()
    {
        $stream = fopen('php://memory', 'w', \false);

        return new FileProcessor(
            $this->getDoctrine(),
            new StreamOutput($stream),
            'test'
        );
    }

    public function testProcess()
    {
        $this->createData();
        $target = $this->createProcessor();
        $target->process();

        $bucket = $this->getBucket('test');
        $cursor = $bucket->find();
        foreach($cursor as $current){
            $this->assertFileMigrated($current);
        }
    }

    protected function assertFileMigrated(array $file)
    {
        $bucket = $this->getBucket('test.files');
        $this->assertNotNull($file);
        $this->assertNotNull($this->getNamespacedValue('metadata', $file));
        $this->assertNull($this->getNamespacedValue('dateuploaded', $file));
        $this->assertNull($this->getNamespacedValue('permissions', $file));
    }
}