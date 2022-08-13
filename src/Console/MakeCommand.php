<?php

namespace ModelFilter\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeCommand extends Command
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
    protected $description = 'Create A Eloquent Model Filter';

    /**
     * Class to create.
     *
     * @var array|string
     */
    protected $class;

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * MakeEloquentFilter constructor.
     *
     * @param  Filesystem  $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->makeClassName()->compileStub();

        $this->info(class_basename($this->getClassName()) .' Created Successfully!');
    }

    public function compileStub()
    {
        if ($this->files->exists($path = $this->getPath())) {
            $this->error("\n\n\t[{$path}] Already Exists! \n");
            exit;
        }

        $this->makeDirectory($path);

        $stubPath = __DIR__ .'/../stubs/modelfilter.stub';

        if (!$this->files->exists($stubPath) || !is_readable($stubPath)) {
            $this->error("File [$stubPath] does not exist or is unreadable!");
            exit;
        }

        $tmp = $this->applyValuesToStub($this->files->get($stubPath));
        $this->files->put($path, $tmp);
    }

    public function applyValuesToStub($stub)
    {
        $className = $this->getClassBasename($this->getClassName());
        $search = ['{{class}}', '{{namespace}}'];
        $replace = [$className, str_replace('\\'.$className, '', $this->getClassName())];

        return str_replace($search, $replace, $stub);
    }

    private function getClassBasename($class)
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }

    public function getPath()
    {
        return $this->laravel->path .'/'. $this->getFileName();
    }

    public function getFileName()
    {
        return str_replace([$this->getAppNamespace(), '\\'], ['', '/'], $this->getClassName().'.php');
    }

    public function getAppNamespace()
    {
        return $this->laravel->getNamespace();
    }

    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }

    public function makeClassName()
    {
        $parts = array_map([Str::class, 'studly'], explode('\\', $this->argument('name')));
        $className = config('modelfilter.namespace', 'App\\Filters\\').implode('\\', $parts);

        if (!Str::endsWith($className, 'Filter')) {
            $className .= 'Filter';
        }

        if (class_exists($className)) {
            $this->error("\n\n\t$className Already Exists!\n");
            exit;
        }

        $this->setClassName($className);

        return $this;
    }

    public function setClassName($name)
    {
        $this->class = $name;

        return $this;
    }

    public function getClassName()
    {
        return $this->class;
    }
}