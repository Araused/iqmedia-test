<?php

namespace app\core;

class View
{
    const VIEWS_PATH = '/core/views/';

    public array $properties;

    public function __construct()
    {
        $this->properties = [];
    }

    /**
     * @param $viewFile
     * @throws Exception
     * @return false|string
     */
    public function render($viewFile, array $properties = [])
    {
        $path = dirname(__DIR__) . self::VIEWS_PATH;
        $template = $path . $viewFile;

        if (!file_exists($template)) {
            throw new Exception('Template file not found');
        }

        ob_start();
        $this->properties = $properties;
        include($template);
        return ob_get_clean();
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