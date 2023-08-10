<?php

namespace app\core;

class View
{
    const VIEWS_PATH = '/core/views/';

    public array $properties;
    public ?string $layout = null;
    public ?string $title = '';

    public function __construct()
    {
        $this->properties = [];
    }

    /**
     * @param string $viewFile
     * @param array $properties
     * @param bool $useLayout
     * @throws Exception
     * @return false|string
     */
    public function render(string $viewFile, array $properties = [], bool $useLayout = true)
    {
        $path = dirname(__DIR__) . self::VIEWS_PATH;
        $template = $path . $viewFile;

        if (!file_exists($template)) {
            throw new Exception('Template file not found');
        }

        ob_start();
        $this->properties = $properties;
        include($template);
        $result = ob_get_clean();

        return !($useLayout && $this->layout !== null)
            ? $result
            : $this->render($this->layout, [
                'title' => $this->title,
                'content' => $result,
            ], false);
    }

    /**
     * @param $k
     * @param $v
     * @return void
     */
    public function __set($k, $v)
    {
        $this->properties[$k] = $v;
    }

    /**
     * @param $k
     * @return mixed
     */
    public function __get($k)
    {
        return $this->properties[$k];
    }
}