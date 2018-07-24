<?php

namespace HuangYi\Filter;

use HuangYi\Filter\Console\FilterMakeCommand;
use HuangYi\Filter\Contracts\ParserContract;
use HuangYi\Filter\Exceptions\InvalidParserException;
use Illuminate\Support\ServiceProvider;

class FilterServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register services.
     *
     * @return void
     * @throws \HuangYi\Filter\Exceptions\InvalidParserException
     */
    public function register()
    {
        $this->mergeAndPublishConfig();

        $this->registerFilter();

        $this->registerFilterParser();

        $this->commands(FilterMakeCommand::class);
    }

    /**
     * Boot filter service.
     *
     * @return void
     */
    public function boot()
    {
        $filters = $this->app->basePath('app/filters.php');

        if ($this->app['files']->exists($filters)) {
            $this->app['files']->requireOnce($filters);
        }
    }

    /**
     * Merge and publish config.
     *
     * @return void
     */
    protected function mergeAndPublishConfig()
    {
        $configPath = __DIR__ . '/../config/filter.php';

        if (function_exists('config_path')) {
            $publishPath = config_path('filter.php');
        } else {
            $publishPath = base_path('config/filter.php');
        }

        $this->mergeConfigFrom($configPath, 'filter');
        $this->publishes([$configPath => $publishPath], 'filter');
    }

    /**
     * Register filter.
     *
     * @return void
     */
    protected function registerFilter()
    {
        $this->app->singleton('filter', function ($app) {
            return new FilterManager($app);
        });

        $this->app->alias('filter', FilterManager::class);
    }

    /**
     * Register filter parser.
     *
     * @return void
     * @throws \HuangYi\Filter\Exceptions\InvalidParserException
     */
    protected function registerFilterParser()
    {
        $parser = $this->app['config']['filter.parser'];

        if (! is_subclass_of($parser, ParserContract::class)) {
            throw new InvalidParserException(
                sprintf('Parser [%s] must implement the %s.', $parser, ParserContract::class)
            );
        }

        $this->app->singleton('filter.parser', function ($app) use ($parser) {
            return new Parser($app);
        });

        $this->app->alias('filter.parser', $parser);
        $this->app->alias('filter.parser', ParserContract::class);
    }

    /**
     * Provides.
     *
     * @return array
     */
    public function provides()
    {
        return ['filter', 'filter.parser'];
    }
}
