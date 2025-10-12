<?php

namespace Navia\Tests;

use Illuminate\Support\Facades\Auth;

class LoginTest extends TestCase
{
    public function testSuccessfulLoginWithDefaultCredentials()
    {
        $this->visit(route('navia.login'))
             ->type('admin@admin.com', 'email')
             ->type('password', 'password')
             ->press(__('navia::generic.login'))
             ->seePageIs(route('navia.dashboard'));
    }

    public function testShowAnErrorMessageWhenITryToLoginWithWrongCredentials()
    {
        session()->setPreviousUrl(route('navia.login'));

        $this->visit(route('navia.login'))
             ->type('john@Doe.com', 'email')
             ->type('pass', 'password')
             ->press(__('navia::generic.login'))
             ->seePageIs(route('navia.login'))
             ->see(__('auth.failed'))
             ->seeInField('email', 'john@Doe.com');
    }

    public function testRedirectIfLoggedIn()
    {
        Auth::loginUsingId(1);

        $this->visit(route('navia.login'))
             ->seePageIs(route('navia.dashboard'));
    }

    public function testRedirectIfNotLoggedIn()
    {
        $this->visit(route('navia.profile'))
             ->seePageIs(route('navia.login'));
    }

    public function testCanLogout()
    {
        Auth::loginUsingId(1);

        $this->visit(route('navia.dashboard'))
             ->press(__('navia::generic.logout'))
             ->seePageIs(route('navia.login'));
    }

    public function testGetsLockedOutAfterFiveAttempts()
    {
        session()->setPreviousUrl(route('navia.login'));

        for ($i = 0; $i <= 5; $i++) {
            $t = $this->visit(route('navia.login'))
                 ->type('john@Doe.com', 'email')
                 ->type('pass', 'password')
                 ->press(__('navia::generic.login'));
        }

        $t->see(__('auth.throttle', ['seconds' => 60]));
    }
}
