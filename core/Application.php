<?php

namespace app\core;

use app\core\Router;
use app\core\View;

class Application
{
    public Router $router;
    public View $view;
    public Model $model;
    protected array $config;

    public function __construct()
    {
        $this->config = require dirname(__DIR__) . '/config.php';

        $this->router = new Router();
        $this->view = new View();
        $this->model = new Model($this->config['db'] ?? []);

        $this->view->layout = $this->config['view']['layout'];
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
            $this->view->title = 'Главная страница';
            echo $this->view->render('_index.php');
        });

        $this->router->post('/create-shortlink', function () {
            $url = $_GET['url'] ?? null;

            if (empty($url)) {
                return $this->renderError();
            }

            $data = $this->model->generateNew($url);

            echo $this->view->render('_ajax-shortlink.php', [
                'full' => $data['landing'],
                'short' => $this->model->getShortlinkFromHash($data['hash']),
                'secret' => $this->model->getStatsLinkFromHash($data['hash']),
            ], false);
        });

        $this->router->get('/stat', function () {
            $key = $_GET['key'] ?? '';
            $data = $this->model->findBySecretKey($key);

            if (empty($data)) {
                return $this->renderError();
            }

            $this->view->title = 'Статистика';
            echo $this->view->render('_stat.php', [
                'full' => $data['landing'],
                'short' => $this->model->getShortlinkFromHash($data['hash']),
                'counter' => $data['counter'],
            ]);
        });

        $this->router->get('/go', function () {
            $hash = $_GET['q'] ?? null;
            $data = $this->model->findByHash($hash);

            if (empty($data)) {
                return $this->renderError();
            }

            $this->model->updateCounterByHash($hash);
            header("Location: {$data['landing']}");
            die();
        });
    }

    /**
     * @TODO вынести это в контроллер при необходимости
     * @return void
     */
    public function renderError()
    {
        $this->view->title = 'Ошибка';
        echo $this->view->render('layout.php', [
            'content' => 'Страница не найдена. Попробуйте проверить URL запроса.',
        ], false);
    }
}