<?php
namespace Dime\Server\Config;

use Slim\Slim;

/**
 * SlimLoader - Yaml configuration loader with Slim adapter.
 *
 * @author Danilo Kühn <dk@nogo-software.de>
 * @package Config
 */
class SlimLoader extends Loader
{
    /**
     * @var Slim
     */
    protected $app;

    /**
     * Constructor.
     *
     * @param Slim $app
     */
    public function __construct(Slim $app)
    {
        $this->app = $app;
    }



    /**
     * Load config into slim configuration
     * @param Slim $app
     */
    public function refresh(Slim $app = null)
    {
        if ($app != null) {
            $this->app = $app;
        }
        $this->app->config($this->config);
    }
}
