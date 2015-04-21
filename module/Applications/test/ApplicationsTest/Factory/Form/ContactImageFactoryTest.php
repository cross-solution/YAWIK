<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace ApplicationTest\Factory\Form;

use Applications\Factory\Form\ContactImageFactory;
use Test\Bootstrap;

class ContactImageFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContactImageFactory
     */
    private $testedObj;

    public function setUp()
    {
        $this->testedObj = new ContactImageFactory();
    }

    /**
     * @expectedException \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function testCreateService()
    {

        $type="Core/FileUpload";
        $name="image";
        $multiple=false;

        $sm = clone Bootstrap::getServiceManager();
        $fm = $sm->get("FormElementManager");

        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );

        /**
         * @todo This Test fails with:
         *  Message was: "Zend\ServiceManager\ServiceManager::get was unable to fetch or create an instance for doctrine.documentmanager.odm_default" at
         *   #0 /home/cbleek/Projects/YAWIK/module/Core/src/Core/Repository/DoctrineMongoODM/DocumentManagerFactory.php(27)
         *
         * reason: ACL in FileUploadFactory
         *
         * eighter the ACL stuff has to be done later or a Test Database has to be used.
         *
         */

        $result = $this->testedObj->createService($fm);



        $this->assertInstanceOf('Application\Form\ContactImage', $result);
    }
}
