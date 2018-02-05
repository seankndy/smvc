<<<<<<< HEAD
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
=======
<?php
namespace SeanKndy\SMVC;

class Model
{
    protected $data = [];
    protected $validate = [];

    public function validateData(array $exclude = []) {
        $validate = $this->validate;
        foreach ($exclude as $evar) {
            if (isset($validate[$evar]))
                unset($validate[$evar]);
        }
/*
        foreach ($validate as $var => $varValidation) {
            foreach ($varValidation as $rule) {
                foreach ($this->validators as $validator) {
                    if (in_array($rule['rule'], $validator->supportedRules())) {
*/
    }

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

    public function get($var, $default = null) {
        return isset($this->data[$var]) ? $this->data[$var] : $default;
    }
}
>>>>>>> b3073afc0b2ef2f249c071a7a00c7b1677579668
