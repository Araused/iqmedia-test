<?php

namespace app\core;

use app\core\Router;
use app\core\View;

class Application
{
    public Router $router;
    public View $view;

    public function __construct()
    {
        $this->router = new Router();
        $this->view = new View();
    }

    /**
     * @return void
     */
    public function run()
    {
        $this->initRoutes();
        $resolve = $this->router->resolve();

        if ($resolve === '404') {
            return $this->renderError();
        }

        echo $resolve;
    }

    /**
     * Вообще, конечно, Application не должен заниматься роутингом или рендером шаблонов
     * Но пока что в полноценном контроллере нужды нет
     * Часто такой код прям в index оставляют - но по-моему, лучше уж внутрь самого Application пока закинуть эту портянку
     * @TODO вынести это в контроллер при необходимости
     * @return void
     */
    private function initRoutes()
    {
        $this->router->get('/', function () {
            echo $this->view->render('layout.php', [
                'title' => 'Главная страница',
                'content' => $this->view->render('_index.php'),
            ]);
        });

        $this->router->post('/new-shortlink', function () {
            $url = $_GET['url'] ?? null;

            if (empty($url)) {
                return $this->renderError();
            }

            //@TODO logic

            return "Render some html with result...";
        });

        $this->router->get('/stat', function () {
            $key = $_GET['key'] ?? null;

            if (empty($url)) {
                return $this->renderError();
            }

            //@TODO logic

            return "Render stat page...";
        });

        $this->router->get('/go', function () {
            $key = $_GET['q'] ?? null;

            if (empty($url)) {
                return $this->renderError();
            }

            //@TODO logic

            return "Update counter & redirect user...";
        });
    }

    /**
     * @TODO вынести это в контроллер при необходимости
     * @return void
     */
    public function renderError()
    {
        echo $this->view->render('layout.php', [
            'title' => 'Ошибка',
            'content' => 'Страница не найдена. Попробуйте проверить URL запроса.',
        ]);
    }
}