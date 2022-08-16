<?php

namespace ModelFilter\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:filter {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model filter';

    /**
     * MakeEloquentFilter constructor.
     *
     * @param  Filesystem  $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct($files);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub()
    {
        return __DIR__ .'/../stubs/modelfilter.stub';
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        $name = Str::studly(parent::getNameInput());

        return Str::endsWith($name, 'Filter') ? $name : $name . 'Filter';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        $configNamespace = config('modelfilter.namespace', 'App\\Filters');

        return Str::startsWith($configNamespace, $rootNamespace) ? $configNamespace : $rootNamespace .'\\'. $configNamespace;
    }
}
