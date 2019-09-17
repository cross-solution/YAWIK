<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Repository;

use PHPUnit\Framework\TestCase;

use CoreTestUtils\TestCase\SetupTargetTrait;
use Jobs\Entity\Category;
use Jobs\Repository\DefaultCategoriesBuilder;
use org\bovigo\vfs\vfsStream;

/**
 * Tests for \Jobs\Repository\DefaultCategoriesBuilder
 *
 * @covers \Jobs\Repository\DefaultCategoriesBuilder
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Repository
 */
class DefaultCategoriesBuilderTest extends TestCase
{
    public function testBuildLoadsConfigFromGlobalPath()
    {
        vfsStream::setup('/');
        vfsStream::create(['config' => ['autoload' => ['jobs.categories.professions.php' => '<?php return ["name"=>"works"];']]]);
        $target = new DefaultCategoriesBuilder('.', [vfsStream::url('config/autoload/')], new Category());

        $category = $target->build('professions');

        $this->assertEquals('works', $category->getName());
    }

    public function testBuildLoadsConfigFromModulePath()
    {
        vfsStream::setup('/');
        vfsStream::create(['module' => ['Jobs' => ['config' => ['jobs.categories.professions.php' => '<?php return ["name"=>"works"];']]]]);
        $target = new DefaultCategoriesBuilder(vfsStream::url('./module/Jobs/config/'), [], new Category());

        $category = $target->build('professions');

        $this->assertEquals('works', $category->getName());
    }

    public function testBuildCreatesEmptyCategory()
    {
        vfsStream::setup('/');

        $target = new DefaultCategoriesBuilder(vfsStream::url('./module'), [], new Category());

        $category = $target->build('professions');

        $this->assertEquals('professions', $category->getName());
    }

    public function testBuildCreatesHirarchy()
    {
        vfsStream::setup('/');
        vfsStream::create(['config' => ['autoload' => ['jobs.categories.professions.php' => '<?php return ["name"=>"works", "children" => ["string", ["name" => "array"]]];']]]);
        $target = new DefaultCategoriesBuilder('.', [vfsStream::url('config/autoload/')], new Category());

        $category = $target->build('professions');

        $this->assertEquals('works', $category->getName());
        $children = $category->getChildren();
        $this->assertEquals(2, $children->count());
        $this->assertEquals('string', $children->first()->getName());
        $this->assertEquals('array', $children->last()->getName());
    }
}
