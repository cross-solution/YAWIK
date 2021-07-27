<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CvTest\Entity;

use Core\Entity\FileInterface;
use Core\Entity\FileMetadataInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use Core\Entity\FileEntity;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Cv\Entity\Attachment;

/**
 * Tests for \Cv\Entity\Attachment
 *
 * @covers \Cv\Entity\Attachment
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class AttachmentTest extends TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    private $target = [
        Attachment::class,
        '@testSetterAndGetter' => [
            'method' => 'setupSetterGetterTarget'
        ]
    ];

    /**
     * @var MockObject|FileMetadataInterface
     */
    private $metadata;

    private $inheritance = [ FileInterface::class ];

    private $properties = [
        [ 'uri', [ 'ignore_setter' => true, 'value' => "/file/Cv.Attachment/test/name" ]]
    ];

    private function setupSetterGetterTarget()
    {
        $target = new Attachment();
        $metadata = $this->createMock(FileMetadataInterface::class);

        $metadata->expects($this->any())
            ->method('getName')
            ->willReturn('name');
        $target->setId('test');
        $target->setMetadata($metadata);

        return $target;
    }
}
