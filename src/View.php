<?php
namespace SeanKndy\SMVC;

class View
{
    protected $app;
    protected $vars;
    static protected $globalVars = [];
    protected $response;
    protected $basePath;
    protected $renderHeaderFooter;

    public function __construct(Application $app, \Psr\Http\Message\ResponseInterface $response, $basePath = '') {
        $this->app = $app;
        $this->response = $response;
        $this->basePath = ($basePath ? $basePath : $this->app->config('view.basePath'));
        if (substr($this->basePath, -1) != '/')
            $this->basePath .= '/';
        $this->renderHeaderFooter = true;
        $this->vars = [];
        return $this;
    }

    public function render($viewName, $vars = []) {
        if (substr($viewName, 0, 1) == '/')
            $viewName = substr($viewName, 1);
        $file = $this->basePath . $viewName . '.php';

        if ($this->renderHeaderFooter) {
            $this->renderFile($this->basePath . 'header.php', $vars);
            $this->renderFile($file, $vars);
            $this->renderFile($this->basePath . 'footer.php', $vars);
        } else
            $this->renderFile($file, $vars);

        return $this->response;
    }

    public static function assignGlobal($var, $val = '') {
        self::$globalVars[$var] = $val;
    }

    public static function getGlobal($var) {
        return isset(self::$globalVars[$var]) ? $self::$globalVars[$var] : '';
    }

    public function assign($var, $val = '') {
        $this->vars[$var] = $val;
        return $this;
    }

    public function get($var) {
        return isset($this->vars[$var]) ? $this->vars[$var] : '';
    }

    public function __isset($var) {
        return isset($this->vars[$var]);
    }

    public function __set($name, $value) {
        $this->assign($name, $value);
    }

    public function __get($name) {
        return $this->get($name);
    }

    public function getApplication() {
        return $this->app;
    }

    public function setRenderHeaderFooter($renderHeaderFooter) {
        $this->renderHeaderFooter = $renderHeaderFooter;
        return $this;
    }

    protected function renderFile($file, $vars = []) {
        if (file_exists($file)) {
            ob_start();

            $_form = new Form($this, $this->app->getRequest());
            extract($vars ? $vars : $this->vars);
            extract(self::$globalVars);

            include($file);
            $output = ob_get_contents();

            ob_end_clean();
            $this->response->getBody()->write($output);
        } else
            throw new \RuntimeException("renderFile(): failed to locate view @ $file");
    }
}
