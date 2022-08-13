<?php

namespace ModelFilter\Tests;

use Illuminate\Foundation\Application;
use Mockery;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Filesystem\Filesystem;
use ModelFilter\ServiceProvider;

class MakeModelFilterCommandTest extends TestCase
{
    protected $command;

    public function createApplication()
    {
        $app = new Application(dirname(__DIR__));

        $app->make(Kernel::class)->bootstrap();

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
