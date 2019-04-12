<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CvTest\Repository\Event;

use PHPUnit\Framework\TestCase;

use Core\Repository\DoctrineMongoODM\Event\AbstractUpdateFilesPermissionsSubscriber;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Cv\Entity\Contact;
use Cv\Entity\ContactImage;
use Cv\Entity\Cv;
use Cv\Repository\Event\UpdateFilesPermissionsSubscriber;
use Doctrine\ODM\MongoDB\Event\OnFlushEventArgs;

/**
 * Tests for \Cv\Repository\Event\UpdateFilesPermissionsSubscriber
 *
 * @covers \Cv\Repository\Event\UpdateFilesPermissionsSubscriber
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Repository
 * @group Cv.Repository.Event
 */
class UpdateFilesPermissionsSubscriberTest extends TestCase
{
    use TestInheritanceTrait;

    /**
     *
     *
     * @var array|UpdateFilesPermissionsSubscriber|UfpsMock
     */
    private $target = [
        UpdateFilesPermissionsSubscriber::class,
        '@testGetFilesAppendsContactImage' => UfpsMock::class,
    ];

    private $inheritance = [ AbstractUpdateFilesPermissionsSubscriber::class ];

    public function testDefaultAttributesValues()
    {
        $this->assertAttributeEquals(Cv::class, 'targetDocument', $this->target);
        $this->assertAttributeEquals([ 'attachments' ], 'filesProperties', $this->target);
    }

    public function testGetFilesAppendsContactImage()
    {
        $document = new Cv();
        $contact = $this
            ->getMockBuilder(Contact::class)
            ->disableOriginalConstructor()
            ->setMethods(['getImage'])
            ->getMock();

        $image = $this
            ->getMockBuilder(ContactImage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $contact->expects($this->once())->method('getImage')->willReturn($image);

        $document->setContact($contact);
        $args = $this->getMockBuilder(OnFlushEventArgs::class)->disableOriginalConstructor()->getMock();

        $this->target->__document__ = $document;
        $actual = $this->target->onFlush($args);

        $this->assertEquals($image, array_pop($actual));
    }
}

class UfpsMock extends UpdateFilesPermissionsSubscriber
{
    public $__document__;

    public function onFlush(OnFlushEventArgs $args)
    {
        return $this->getFiles($this->__document__);
    }
}
