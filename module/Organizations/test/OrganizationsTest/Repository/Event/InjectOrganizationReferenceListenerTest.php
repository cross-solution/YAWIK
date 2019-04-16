<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace OrganizationsTest\Repository\Event;

use PHPUnit\Framework\TestCase;

use Auth\Entity\User;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Organizations\Repository\Event\InjectOrganizationReferenceListener;
use Organizations\Entity\Organization;

/**
 * test for the InjectOrganizationReferenceListener
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Organizations
 * @group Organizations.Repository
 * @group Organizations.Repository.Event
 */
class InjectOrganizationReferenceListenerTest extends TestCase
{

    /**
     * Correct interface?
     */
    public function testImplementsInterface()
    {
        $target = new InjectOrganizationReferenceListener();

        $this->assertInstanceOf('\Doctrine\Common\EventSubscriber', $target);
    }

    /**
     * Does the listener subscribes to the correct events?
     */
    public function testListenerSubsribesToExpectedEvents()
    {
        $target = new InjectOrganizationReferenceListener();

        $expected = array(Events::postLoad);
        $actual = $target->getSubscribedEvents();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Post load hook must do nothing, if updated document is not UserInterface!
     */
    public function testPostLoadHookDoesNothingIfDocumentIsNotUserInterface()
    {
        $target = new InjectOrganizationReferenceListener();
        $dm = $this->getMockBuilder('\Doctrine\ODM\MongoDB\DocumentManager')->disableOriginalConstructor()->getMock();
        $doc = new Organization();

        $dm->expects($this->never())->method('getRepository');
        $args = new LifecycleEventArgs($doc, $dm);

        $result = $target->postLoad($args);
        $this->assertNull($result);
    }

    /**
     * post load hook must inject organization reference to UserInterface instances!
     */
    public function testPostLoadHookInjectsOrganizationReferenceIfDocumentIsUserInterface()
    {
        $target = new InjectOrganizationReferenceListener();
        $dm = $this->getMockBuilder('\Doctrine\ODM\MongoDB\DocumentManager')->disableOriginalConstructor()->getMock();
        $doc = new User();
        $rep = $this->getMockBuilder('\Organizations\Repository\Organization')->disableOriginalConstructor()->getMock();

        $dm->expects($this->once())->method('getRepository')->with('Organizations\Entity\Organization')
           ->willReturn($rep);

        $doc->setId('test1234');

        $args = new LifecycleEventArgs($doc, $dm);

        $result = $target->postLoad($args);

        $this->assertNull($result);
        $ref = $doc->getOrganization();

        $this->assertInstanceOf('\Organizations\Entity\OrganizationReference', $ref);
        $this->assertAttributeSame($rep, '_repository', $ref);
        $this->assertAttributeEquals($doc->getId(), '_userId', $ref);
    }
}
