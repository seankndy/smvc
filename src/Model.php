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
