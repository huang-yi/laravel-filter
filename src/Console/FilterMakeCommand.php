<?php

namespace HuangYi\Filter\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

class FilterMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:filter {name : Filter name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new filter class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Filter';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        parent::handle();

        $this->nameFilter();
    }

    /**
     * Append name filter.
     *
     * @return void
     */
    protected function nameFilter()
    {
        if (! $this->files->exists($this->getNamePath())) {
            $this->files->copy(__DIR__.'/stubs/filters.stub', $this->getNamePath());
        }

        $name = sprintf(
            "\nFilter::name('%s', %s::class);\n",
            $this->getNameInput(),
            $this->qualifyClass($this->getNameInput())
        );

        $this->files->append($this->getNamePath(), $name);
    }

    /**
     * Get name path.
     *
     * @return string
     */
    protected function getNamePath()
    {
        return $this->laravel['path'].'/filters.php';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/filter.stub';
    }

    /**
     * Get the default namespace for the filter class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'Filters';
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        $pieces = array_map('ucfirst', explode('.', $name));

        $rootNamespace = $this->rootNamespace();

        return $this->getDefaultNamespace($rootNamespace)."\\".implode("\\", $pieces);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the filter'],
        ];
    }
}
