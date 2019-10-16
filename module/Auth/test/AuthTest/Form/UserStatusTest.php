<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Form;

use PHPUnit\Framework\TestCase;

use Auth\Form\UserStatus;
use Core\Form\SummaryForm;

class UserStatusTest extends TestCase
{
    /**
     * @var UserStatus
     */
    private $form;

    protected function setUp(): void
    {
        $this->form = new UserStatus();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(UserStatus::class, $this->form);
        $this->assertInstanceOf(SummaryForm::class, $this->form);
        $this->assertSame('Auth/UserStatusFieldset', $this->form->getBaseFieldset());
    }
}
