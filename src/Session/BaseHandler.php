<?php
namespace SeanKndy\SMVC\Session;

class BaseHandler extends \SessionHandler
{
    public function set($var, $val) {
        return ($_SESSION[$var] = $val);
    }

    public function __set($var, $val) {
        return $this->set($var, $val);
    }

    public function get($var) {
        return isset($_SESSION[$var]) ? $_SESSION[$var] : '';
    }

    public function __get($var) {
        return $this->get($var);
    }

    public function clear() {
        $_SESSION = [];
    }

    public function __unset($var) {
        if (isset($_SESSION[$var])) {
            unset($_SESSION[$var]);
        }
    }

    public function __isset($var) {
        return isset($_SESSION[$var]);
    }
}
