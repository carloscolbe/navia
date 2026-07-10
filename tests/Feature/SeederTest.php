<?php

namespace Navia\Tests\Feature;

use Navia\Tests\TestCase;

class SeederTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->install();
    }

    /**
     * Test manually seeding is working.
     */
    public function testNaviaDatabaseSeederCanBeCalled()
    {
        $exception = null;

        try {
            $this->artisan('db:seed', ['--class' => 'NaviaDatabaseSeeder']);
        } catch (\Exception $exception) {
        }

        $this->assertNull($exception, 'An exception was thrown');
    }
}
