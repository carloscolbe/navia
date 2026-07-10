<?php

namespace Navia\Tests\Unit\Actions;

use Navia\Actions\AbstractAction;
use Navia\Facades\Navia;
use Navia\Tests\TestCase;

class AbstractActionTest extends TestCase
{
    /**
     * The users DataType instance.
     *
     * @var \Navia\Models\DataType
     */
    protected $userDataType;

    /**
     * A dummy user instance.
     *
     * @var \Navia\Models\User
     */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->userDataType = Navia::model('DataType')->where('name', 'users')->first();
        $this->user = \Navia\Models\User::factory()->create();
    }

    /**
     * This test checks that `getRoute` method calls the `getDefaultRoute`
     * method if the given key is empty.
     */
    public function testGetRouteWithEmptyKey()
    {
        $stub = new class(null, null) extends DummyAction {
            public function getDefaultRoute()
            {
                return true;
            }
        };

        // The `getDefaultRoute` method is called as default inside the
        // `getRoute` method to retrieve the route.
        $this->assertTrue($stub->getRoute($this->userDataType->name));
    }

    /**
     * This test checks that `getRoute` method calls the expected method when a
     * key is given.
     */
    public function testGetRouteWithCustomKey()
    {
        $stub = new class(null, null) extends DummyAction {
            // The key that's passed to the `getRoute` method will be capitalized
            // and putted between 'get' and 'Route'. Calling `getRoute('custom')`
            // will call the `getCustomRoute` method if it's defined.
            public function getCustomRoute()
            {
                return true;
            }
        };

        $this->assertTrue($stub->getRoute('custom'));
    }

    /**
     * This test checks that `getAttributes` method will give us the expected
     * output.
     */
    public function testConvertAttributesToHtml()
    {
        $stub = new class(null, null) extends DummyAction {
            public function getAttributes()
            {
                return [
                    'class'   => 'class1 class2',
                    'data-id' => 5,
                    'id'      => 'delete-5',
                ];
            }
        };

        $this->assertEquals('class="class1 class2" data-id="5" id="delete-5"', $stub->convertAttributesToHtml());
    }

    /**
     * This test checks that `shouldActionDisplayOnDataType` method returns true
     * if the action should be displayed for every data type.
     */
    public function testShouldActionDisplayOnDataTypeWithDefaultDataType()
    {
        $stub = new DummyAction($this->userDataType, $this->user);

        $this->assertTrue($stub->shouldActionDisplayOnDataType());
    }

    /**
     * This test checks that `shouldActionDisplayOnDataType` method returns true
     * if the action should only be displayed for a specific data type.
     */
    public function testTrueIsReturnedIfDataTypeMatchesTheOneWhereTheActionWasCreatedFor()
    {
        $stub = new class($this->userDataType, $this->user) extends DummyAction {
            public function getDataType()
            {
                return 'users';
            }
        };

        $this->assertTrue($stub->shouldActionDisplayOnDataType());
    }

    /**
     * This test checks that `shouldActionDisplayOnDataType` method returns false
     * if the action should only be displayed for a specific data type.
     */
    public function testFalseIsReturnedIfDataTypeDoesNotMatchesTheOneWhereTheActionWasCreatedFor()
    {
        $stub = new class($this->userDataType, $this->user) extends DummyAction {
            public function getDataType()
            {
                return 'not users'; // different data type
            }
        };

        $this->assertFalse($stub->shouldActionDisplayOnDataType());
    }
}

class DummyAction extends AbstractAction
{
    public function getTitle()
    {
        return 'dummy';
    }

    public function getIcon()
    {
        return 'navia-eye';
    }

    public function getDefaultRoute()
    {
        return 'dummy-route';
    }
}
