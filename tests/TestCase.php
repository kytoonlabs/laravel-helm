<?php

namespace Kytoonlabs\LaravelHelm\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Kytoonlabs\LaravelHelm\Helm;
use Kytoonlabs\LaravelHelm\HelmServiceProvider;

abstract class TestCase extends Orchestra
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        Helm::setPath(getenv('HELM_BINARY_PATH') ?: '/usr/local/bin/helm');
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app)
    {
        return [
            HelmServiceProvider::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'wslxrEFGWY6GfGhvN9L3wH3KSRJQQpBD');
    }
}
