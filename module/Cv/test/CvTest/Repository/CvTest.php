<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CvTest\Repository;

use PHPUnit\Framework\TestCase;

use Auth\Entity\User;
use CoreTestUtils\TestCase\FunctionalTestCase;
use Cv\Entity\Cv;
use Zend\ServiceManager\ServiceManager;

/**
 * Class CvTest
 * @package CvTest
 * @covers \Cv\Repository\Cv
 */
class CvTest extends FunctionalTestCase
{
    /**
     * @var ServiceManager
     */
    protected static $sm;

    /**
     * Current authenticated user
     * @var User
     */
    protected static $user;

    protected function setUp(): void
    {
        parent::setUp();

        if (!is_object(static::$sm)) {
            static::$sm = $this->serviceLocator;
        }
        if (!is_object(static::$user)) {
            $this->loginAsUser();
            static::$user = $this->activeUser;
        } else {
            $this->activeUser = static::$user;
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->removeCvData();
    }

    protected function removeCvData()
    {
        $sm = static::$sm;
        /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $sm->get('doctrine.documentmanager.odm_default');
        $repo = $dm->getRepository('Cv\Entity\Cv');
        $data = $repo->findDraft(static::$user);
        if (!is_null($data)) {
            /* @var \Cv\Entity\Cv $cv */
            foreach ($data as $cv) {
                $dm->remove($cv);
                $dm->flush($cv);
            }
        }
    }

    public function testFindByDraft()
    {
        $this->removeCvData();
        /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->serviceLocator->get('doctrine.documentmanager.odm_default');
        $user = $this->activeUser;
        $repo = $dm->getRepository('Cv\Entity\Cv');
        $cv = new Cv();
        $cv
            ->setUser($user)
            ->setIsDraft(true);
        $dm->persist($cv);
        $dm->flush($cv);

        $this->assertInstanceOf(Cv::class, $repo->findDraft($user));
    }
}
