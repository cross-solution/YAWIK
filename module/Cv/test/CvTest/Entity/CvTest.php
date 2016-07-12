<?php

/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @author fedys
 * @license   AGPLv3
 */

namespace CvTest\Entity;


use Auth\Entity\Info;
use Auth\Entity\User;
use Core\Collection\IdentityWrapper;
use Core\Entity\AbstractIdentifiableEntity;
use Core\Entity\DraftableEntityInterface;
use Core\Entity\Permissions;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Cv\Entity\Contact;
use Cv\Entity\Cv;
use Cv\Entity\CvInterface;
use Cv\Entity\PreferredJob;
use Cv\Entity\Status;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class CvTest
 * @package CvTest\Entity
 * @covers \Cv\Entity\Cv
 */
class CvTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    private $target = Cv::class;

    private $inheritance = [ AbstractIdentifiableEntity::class, CvInterface::class, DraftableEntityInterface::class ];

    public function propertiesProvider() {
        $coll = new ArrayCollection();
        $prefJob = new PreferredJob();
        $defaultOptions = [ 'default' => $coll, 'value' => $coll ];
        $indexedByOptions = function($prop) use ($coll) {
            return [ $prop, [ 'value' => $coll, 'getter_method' => 'get*IndexedById', 'expect' => '@' . IdentityWrapper::class]];
        };
        $permissions = $this
            ->getMockBuilder(Permissions::class)
            ->disableOriginalConstructor()
            ->setMethods(['grant', 'revoke'])
            ->getMock();

        $permissions->expects($this->once())->method('revoke');
        $permissions->expects($this->once())->method('grant');

        return [
            [ 'educations', $defaultOptions],
            [ 'employments',$defaultOptions],
            [ 'skills',     $defaultOptions],
            [ 'languageSkills', $defaultOptions],
            [ 'nativeLanguages', ['default' => [], 'value' => []]],
            [ 'preferredJob', [ 'default' => $prefJob, 'value' => $prefJob]],
            [ 'user', new User() ],
            [ 'contact', new Contact()],
            [ 'contact', ['value' => new Info(), 'expect' => '@' . Contact::class ]],
            $indexedByOptions('languageSkills'),
            $indexedByOptions('employments'),
            $indexedByOptions('educations'),
            $indexedByOptions('skills'),
            [ 'isDraft', [ 'value' => false, 'getter_method' => '*']],
            [ 'status', ['value' => new Status(), 'default' => new Status() ]],
            [ 'status', ['value' => Status::NONPUBLIC, 'expect' => '@' . Status::class]],
            [ 'attachments', $defaultOptions],
            [ 'user', ['value' => new User(), 'ignore_getter' => true,
                        'pre' => function() use ($permissions) { $this->target->setUser(new User())->setPermissions($permissions); },
                        'post' => function() use ($permissions) { $permissions->__phpunit_verify(); }
            ]],
        ];
    }
}
