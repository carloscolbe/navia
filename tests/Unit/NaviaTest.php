<?php

namespace Navia\Tests\Unit;

use Illuminate\Support\Facades\Config;
use Navia\Facades\Navia;
use Navia\Tests\TestCase;

class NaviaTest extends TestCase
{
    /**
     * Dimmers returns an array filled with widget collections.
     *
     * This test will make sure that the dimmers method will give us an array
     * of the collection of the configured widgets.
     */
    public function testDimmersReturnsCollectionOfConfiguredWidgets()
    {
        Config::set('navia.dashboard.widgets', [
            'Navia\\Tests\\Stubs\\Widgets\\AccessibleDimmer',
            'Navia\\Tests\\Stubs\\Widgets\\AccessibleDimmer',
        ]);

        $dimmers = Navia::dimmers();

        $this->assertEquals(2, $dimmers[0]->count());
    }

    /**
     * Dimmers returns an array filled with widget collections.
     *
     * This test will make sure that the dimmers method will give us a
     * collection of the configured widgets which also should be displayed.
     */
    public function testDimmersReturnsCollectionOfConfiguredWidgetsWhichShouldBeDisplayed()
    {
        Config::set('navia.dashboard.widgets', [
            'Navia\\Tests\\Stubs\\Widgets\\AccessibleDimmer',
            'Navia\\Tests\\Stubs\\Widgets\\InAccessibleDimmer',
            'Navia\\Tests\\Stubs\\Widgets\\InAccessibleDimmer',
        ]);

        $dimmers = Navia::dimmers();

        $this->assertEquals(1, $dimmers[0]->count());
    }

    /**
     * Dimmers returns an array filled with widget collections.
     *
     * Tests that we build N / 3 (rounded up) widget collections where
     * N is the total amount of widgets set in configuration
     */
    public function testCreateEnoughDimmerCollectionsToContainAllAvailableDimmers()
    {
        Config::set('navia.dashboard.widgets', [
            'Navia\\Tests\\Stubs\\Widgets\\AccessibleDimmer',
            'Navia\\Tests\\Stubs\\Widgets\\AccessibleDimmer',
            'Navia\\Tests\\Stubs\\Widgets\\AccessibleDimmer',
            'Navia\\Tests\\Stubs\\Widgets\\AccessibleDimmer',
            'Navia\\Tests\\Stubs\\Widgets\\AccessibleDimmer',
        ]);

        $dimmers = Navia::dimmers();

        $this->assertEquals(2, count($dimmers));
        $this->assertEquals(3, $dimmers[0]->count());
        $this->assertEquals(2, $dimmers[1]->count());
    }
}
