<?php

namespace app\core;

use app\core\Router;

class Application
{
    public Router $router;

    public function __construct($ROOT_DIR = '')
    {
        $this->router = new Router();
    }

    public function run()
    {
        $this->initRoutes();
        echo $this->router->resolve();
    }

    private function initRoutes()
    {
        $this->router->get('/', function () {
            return "Render main page...";
        });

        $this->router->post('/new-shortlink', function () {
            $url = $_GET['url'];

            if (empty($url)) {
                return '404';
            }

            //@TODO logic

            return "Render some html with result...";
        });

        $this->router->get('/stat', function () {
            $key = $_GET['key'];

            if (empty($key)) {
                return '404';
            }

            //@TODO logic

            return "Render stat page...";
        });

        $this->router->get('/go', function () {
            $key = $_GET['h'];

            if (empty($key)) {
                return '404';
            }

            //@TODO logic

            return "Update counter & redirect user...";
        });
    }
}