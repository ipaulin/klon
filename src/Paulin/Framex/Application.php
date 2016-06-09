<?php
namespace Paulin\Framex;

use Philo\Blade\Blade;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class Application extends Container
{
    private $dir;
    private $router;
    private $request;
    private $dispatcher;
    private $env;


    /**
     * @param string $env Environment
     */
    public function __construct($env = 'prod')
    {
        $this->dir = __DIR__;

        $this->env = $env;

        $this->dispatcher = new Dispatcher();

//        $this->router = new Router($this->dispatcher, $this);
        $this->singleton('Illuminate\Routing\Router', function() {
//            $request = new Request();
            return new Router($this->dispatcher, $this);
        });

        $this->router = $this->make('Illuminate\Routing\Router');

        $this->getRoutes();

        $this->singleton('Illuminate\Http\Request', function() {
            $request = new Request();
            return $request::createFromGlobals();
        });

        $this->request = $this->make('Illuminate\Http\Request');

        $this->setViewComponent();


    }

    /**
     * Add Blade templating into container
     */
    private function setViewComponent()
    {
        $this['compiled_dir'] = $this->dir . '/../../../app/cache/compiled_views';
        $this['views_dir'] = [$this->dir . '/../../../app/Views'];

        $this->bind('Philo\Blade\Blade', function() {
            return new Blade($this['views_dir'], $this['compiled_dir']);
        });
    }

    /**
     * Include file with defined routes
     */
    private function getRoutes()
    {
        require $this->dir . '/../../../app/routes.php';
    }

    /**
     * Check if development environment is set
     * @return bool
     */
    private function isDevEnv()
    {
        return $this->env === 'dev';
    }


    public function run()
    {
        try {
            $response = $this->router->dispatch($this->request);
        } catch(NotFoundHttpException $e) {
            $response = new Response('Not Found', 404);
        } catch(\Exception $e) {
            $content = 'An error occurred';
            $content .= $this->isDevEnv() ? ' - ' . $e->getMessage()  : '';
            $response = new Response($content, 500);
        }

        $response->send();
    }
}