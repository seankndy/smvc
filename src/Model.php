<?php
namespace SeanKndy\SMVC;

class Model
{
    /*
     * used to hold data variables for this model
     */
    protected $data;
    
    public function set($var, $val = '') {
        if (is_array($var)) {
            foreach ($var as $k => $v) {
                $this->data[$k] = $v;
            }
        } else {
            $this->data[$var] = $val;
        }
        return $this;
    }
    
    public function get($var) {
        return isset($this->data[$var]) ? $this->data[$var] : '';
    }
 
}
