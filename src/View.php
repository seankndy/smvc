<?php
namespace SeanKndy\SMVC;

class View
{
    protected $vars;
    static protected $globalVars = [];
    protected $response;
    protected $basePath;
    protected $renderHeaderFooter;
    
    public function __construct(\Psr\Http\Message\ResponseInterface $response, $basePath = '../views/') {
        $this->response = $response;
        if (substr($basePath, -1) != '/')
            $basePath .= '/';
        $this->basePath = $basePath;
        $this->renderHeaderFooter = true;
        $this->vars = [];
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
    }
    
    public function get($var) {
        return isset($this->vars[$var]) ? $this->vars[$var] : '';
    }
    
    public function __set($name, $value) {
        $this->assign($name, $value);
    }
    
    public function __get($name) {
        return $this->get($name);
    }
    
    public function setRenderHeaderFooter($renderHeaderFooter) {
        $this->renderHeaderFooter = $renderHeaderFooter;
    }
    
    protected function renderFile($file, $vars = []) {
        if (file_exists($file)) {
            ob_start();
            extract($vars ? $vars : $this->vars);
            extract(self::$globalVars);
            include($file);
            $output = ob_get_contents();
            ob_end_clean();
            $this->response->getBody()->write($output);
        } else
            throw new \Exception("renderFile(): failed to locate view @ $file");
    }
}

