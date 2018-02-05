<?php
namespace SeanKndy\SMVC;

class Model
{
    /*
     * Store data for this model
     *
     * @var array key-value assoc-array
     */
    protected $data = [];
    
    public function set($var, $val = '') {
        if (is_array($var)) {
            $this->data = array_merge($this->data, $var);
        } else {
            $this->data[$var] = $val;
        }
        return $this;
    }

    public function get($var) {
        return isset($this->data[$var]) ? $this->data[$var] : '';
    }

    public function __get($var) {
        return $this->get($var);
    }

    public function __set($var, $val) {
        return $this->set($var, $val);
    }

    public function __isset($var) {
        return isset($this->data[$var]);
    }
}
