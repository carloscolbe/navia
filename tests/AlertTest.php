<?php

namespace Navia\Tests;

use Navia\Alert;
use Navia\Facades\Navia;

class AlertTest extends TestCase
{
    public function testAlertsAreRegistered()
    {
        $alert = (new Alert('test', 'warning'))
            ->title('Title');

        Navia::addAlert($alert);

        $alerts = Navia::alerts();

        $this->assertCount(1, $alerts);
    }

    public function testComponentRenders()
    {
        Navia::addAlert((new Alert('test', 'warning'))
            ->title('Title')
            ->text('Text')
            ->button('Button', 'http://example.com', 'danger'));

        $alerts = Navia::alerts();

        $this->assertEquals('<strong>Title</strong>', $alerts[0]->components[0]->render());
        $this->assertEquals('<p>Text</p>', $alerts[0]->components[1]->render());
        $this->assertEquals("<a href='http://example.com' class='btn btn-danger'>Button</a>", $alerts[0]->components[2]->render());
    }

    public function testAlertsRenders()
    {
        Navia::addAlert((new Alert('test', 'warning'))
            ->title('Title')
            ->text('Text')
            ->button('Button', 'http://example.com', 'danger'));

        Navia::addAlert((new Alert('foo'))
            ->title('Bar')
            ->text('Foobar')
            ->button('Link', 'http://example.org'));

        $this->assertXmlStringEqualsXmlFile(
            __DIR__.'/rendered_alerts.html',
            view('navia::alerts')->render()
        );
    }
}
