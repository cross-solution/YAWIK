<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace OrganizationsTest\Factory\Controller;

use Interop\Container\ContainerInterface;
use Jobs\Options\JobboardSearchOptions;
use Organizations\Controller\ProfileController;
use Organizations\Factory\Controller\ProfileControllerFactory;
use Jobs\Repository\Job as JobRepository;
use Zend\I18n\Translator\TranslatorInterface;
use Organizations\Repository\Organization as OrganizationRepository;
use Organizations\ImageFileCache\Manager as ImageFileCacheManager;

/**
 * Class ProfileControllerFactoryTest
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @since 0.30
 * @covers \Organizations\Factory\Controller\ProfileControllerFactory
 * @package OrganizationsTest\Factory\Controller
 */
class ProfileControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testInvokation()
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $repository = $this->createMock(OrganizationRepository::class);
        $jobRepository = $this->createMock(JobRepository::class);
        $imageFileCacheManager = $this->createMock(ImageFileCacheManager::class);

        $container = $this->createMock(ContainerInterface::class);

        $options = $this->createMock(JobboardSearchOptions::class);
        $options->expects($this->once())
            ->method('getPerPage')
            ->willReturn(10)
        ;
        $container->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['repositories',$container],
                ['Organizations/Organization',$repository],
                ['translator',$translator],
                ['Organizations\ImageFileCache\Manager',$imageFileCacheManager],
                ['Jobs/Job',$jobRepository],
                ['Jobs/JobboardSearchOptions',$options]
            ])
        ;

        $factory = new ProfileControllerFactory();
        $controller = $factory($container,'some-name');
        $this->assertInstanceOf(ProfileController::class,$controller);
    }
}
