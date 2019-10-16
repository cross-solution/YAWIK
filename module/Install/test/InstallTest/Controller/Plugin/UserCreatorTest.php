<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace InstallTest\Controller\Plugin;

use PHPUnit\Framework\TestCase;

use Auth\Entity\Filter\CredentialFilter;
use Auth\Entity\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Install\Controller\Plugin\UserCreator;
use Install\Filter\DbNameExtractor;
use Auth\Repository\User as UserRepository;

/**
 * Tests for \Install\Controller\Plugin\UserCreator
 *
 * @covers \Install\Controller\Plugin\UserCreator
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Install
 * @group Install.Controller
 * @group Install.Controller.Plugin
 */
class UserCreatorTest extends TestCase
{

    /**
     * Class under test
     *
     * @var UserCreator
     */
    protected $target;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $credentialFilter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $documentManager;

    protected function setUp(): void
    {
        $credentialFilter = $this->createMock(CredentialFilter::class);
        $dm = $this->createMock(DocumentManager::class);

        $this->target = new UserCreator($credentialFilter, $dm);
        $this->credentialFilter= $credentialFilter;
        $this->documentManager = $dm;
    }

    public function testExtendsAbstractPlugin()
    {
        $this->assertInstanceOf('\Zend\Mvc\Controller\Plugin\AbstractPlugin', $this->target);
    }

    public function testProcess()
    {
        $credential = $this->credentialFilter;
        $credential->expects($this->exactly(2))
            ->method('filter')
            ->with('password')
            ->willReturn('filtered-password')
        ;

        $repository = $this->createMock(UserRepository::class);
        $repository->expects($this->any())
            ->method('store')
            ->with($this->isInstanceOf(User::class))
        ;

        $dm = $this->documentManager;
        $dm->expects($this->exactly(2))
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($repository)
        ;

        $target = $this->target;
        $target->process('foo', 'password', 'foo@bar.com');

        $repository->expects($this->any())
            ->method('store')
            ->willThrowException(new \Exception('some-exception'))
        ;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('some-exception');
        $target->process('foo', 'password', 'foo@bar.com');
    }
}
