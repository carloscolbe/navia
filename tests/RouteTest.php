<?php

namespace Navia\Tests;

class RouteTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testGetRoutes()
    {
        $this->disableExceptionHandling();

        $this->visit(route('navia.login'));
        $this->type('admin@admin.com', 'email');
        $this->type('password', 'password');
        $this->press(__('navia::generic.login'));

        $urls = [
            route('navia.dashboard'),
            route('navia.media.index'),
            route('navia.settings.index'),
            route('navia.roles.index'),
            route('navia.roles.create'),
            route('navia.roles.show', 1),
            route('navia.roles.edit', 1),
            route('navia.users.index'),
            route('navia.users.create'),
            route('navia.users.show', 1),
            route('navia.users.edit', 1),
            route('navia.posts.index'),
            route('navia.posts.create'),
            route('navia.posts.show', 1),
            route('navia.posts.edit', 1),
            route('navia.pages.index'),
            route('navia.pages.create'),
            route('navia.pages.show', 1),
            route('navia.pages.edit', 1),
            route('navia.categories.index'),
            route('navia.categories.create'),
            route('navia.categories.show', 1),
            route('navia.categories.edit', 1),
            route('navia.menus.index'),
            route('navia.menus.create'),
            route('navia.menus.show', 1),
            route('navia.menus.edit', 1),
            route('navia.database.index'),
            route('navia.bread.edit', 'categories'),
        ];

        foreach ($urls as $url) {
            $response = $this->call('GET', $url);
            $this->assertEquals(200, $response->status(), $url.' did not return a 200');
        }

        // The Database Manager is disabled: its create/edit routes redirect
        // back to the database index with a warning instead of failing.
        $gatedUrls = [
            route('navia.database.edit', 'categories'),
            route('navia.database.create'),
        ];

        foreach ($gatedUrls as $url) {
            $response = $this->call('GET', $url);
            $this->assertEquals(302, $response->status(), $url.' did not redirect');
            $this->assertEquals(route('navia.database.index'), $response->headers->get('Location'));
        }
    }
}
