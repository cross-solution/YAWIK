<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace AuthTest\Entity;

use PHPUnit\Framework\TestCase;

use Auth\Entity\Token;

/**
 * Tests for Token Entity
 *
 * @covers \Auth\Entity\Token
 * @coversDefaultClass \Auth\Entity\Token
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Auth
 * @group  Auth.Entity
 */
class TokenTest extends TestCase
{
    /**
     * The "Class under Test"
     *
     * @var Token
     */
    private $target;

    protected function setUp(): void
    {
        $this->target = new Token();
    }

    /**
     * @testdox Allows to use String or DateTime
     * @covers \Auth\Entity\Token::getHash
     * @covers \Auth\Entity\Token::setHash
     */
    public function testGetSetHash()
    {
        $input = "thisIsMyHash";
        $this->target->setHash($input);
        $this->assertEquals($input, $this->target->getHash());
    }

    /**
     * @testdox Allows to use String or DateTime
     * @covers \Auth\Entity\Token::getExpirationDate
     * @covers \Auth\Entity\Token::setExpirationDate
     */
    public function testGetSetExpirationDate()
    {
        $input = "01.01.2016";
        $this->target->setExpirationDate($input);
        $this->assertEquals(new \DateTime($input), $this->target->getExpirationDate());
        $input = new \DateTime();
        $this->target->setExpirationDate($input);
        $this->assertEquals($input, $this->target->getExpirationDate());
    }
}
