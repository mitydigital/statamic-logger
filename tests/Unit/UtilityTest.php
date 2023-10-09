<?php

use Illuminate\Routing\Route;
use MityDigital\StatamicLogger\Facades\StatamicLogger;
use MityDigital\StatamicLogger\ServiceProvider;
use Statamic\Auth\Permission;
use Statamic\Facades\Utility;

beforeEach(function () {
    $this->utility = Utility::find('statamic-logger');
});

it('only defines the utility when enabled', function () {
    expect(StatamicLogger::enabled())->toBeTrue()
        ->and($this->utility)->not()->toBeNull();

    // disable and re-run
    config()->set('statamic-logger.enabled', false);
    $provider = new ServiceProvider(app());
    callProtectedMethod($provider, 'configureUtility', ['disabled-logger']);

    $noUtility = Utility::find('disabled-logger');

    expect($noUtility)->toBeNull();
});

it('has defined the logger utility', function () {
    expect($this->utility)->not()->toBeNull();
});

it('has a title, description and nav', function () {
    expect($this->utility->title())->toBe(__('statamic-logger::utility.title'))
        ->and($this->utility->navTitle())->toBe(__('statamic-logger::utility.nav_title'))
        ->and($this->utility->description())->toBe(__('statamic-logger::utility.description'));
});

it('has an icon', function () {
    expect($this->utility->icon())->not()->toBeNull();
});

it('has defined the correct utility routes', function () {
    $routePrefix = 'statamic.cp.utilities.statamic-logger';
    $requiredRoutes = [
        'show',
        'download',
    ];

    // get the routes
    $router = app('router');
    $routes = collect($router->getRoutes());
    $utilityRoutes = $routes
        ->filter(fn (Route $route) => str_starts_with($route->getName(), $routePrefix))
        ->map(fn (Route $route) => $route->getName())
        ->values();

    expect($utilityRoutes)->toHaveCount(count($requiredRoutes));

    foreach ($requiredRoutes as $name) {
        $name = $routePrefix.'.'.$name;

        expect($utilityRoutes->search($name))->not()->toBeFalse(); // because "search" will return zero for the first
    }
});

it('has a permission added', function () {
    // boot permissions
    \Statamic\Facades\Permission::boot();

    // find the permission
    $permission = \Statamic\Facades\Permission::all()
        ->filter(fn (Permission $permission) => $permission->value() === 'access statamic-logger utility')
        ->first();

    expect($permission)->not()->toBeNull()
        ->and($permission->group())->toBe('utilities');
});
