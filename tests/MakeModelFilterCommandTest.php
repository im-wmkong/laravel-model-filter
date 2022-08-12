<?php

namespace ModelFilter\Tests;

use Mockery;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Filesystem\Filesystem;
use ModelFilter\ServiceProvider;

class MakeModelFilterCommandTest extends TestCase
{
    protected $command;

    public function createApplication()
    {
        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        $app->register(ServiceProvider::class);

        return $app;
    }

    public function setUp(): void
    {
        $filesystem = Mockery::mock(Filesystem::class);
        $this->command = Mockery::mock('ModelFilter\Commands\MakeModelFilter[argument]', [$filesystem]);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @dataProvider modelClassProvider
     */
    public function testMakeClassName($argument, $class)
    {
        $this->command->shouldReceive('argument')->andReturn($argument);
        $this->command->makeClassName();
        $this->assertEquals("App\\ModelFilters\\$class", $this->command->getClassName());
    }

    public function modelClassProvider()
    {
        return [
            ['User', 'UserFilter'],
            ['Admin\\User', 'Admin\\UserFilter'],
            ['UserFilter', 'UserFilter'],
            ['user-filter', 'UserFilter'],
            ['adminUser', 'AdminUserFilter'],
            ['admin-user', 'AdminUserFilter'],
            ['admin-user\\user-filter', 'AdminUser\\UserFilter'],
        ];
    }
}
