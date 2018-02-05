<?php
namespace SeanKndy\SMVC\Validation;

class Validation
{
    protected $field;
    protected $rules = [];
    protected $required = false; // is this field's presence required?
    protected $allowEmpty = false;

    public function __construct($field) {
        $this->field = $field;
    }

    public function setAllowEmpty($allowEmpty = true) {
        $this->allowEmpty = $allowEmpty;
        return $this;
    }

    public function allowEmpty() {
        return $this->allowEmpty ? true : false;
    }

    public function setRequired($req) {
        $this->required = $req;
    }

    public function isRequired() {
        return $this->required ? true : false;
    }

    public function addRule($rule, array $options = []) {
        $this->rules[$rule] = $options;
    }

    public function getRules() {
        return $this->rules;
    }

    public function validate(array $ruleClasses, $value) {
        foreach ($this->rules as $rule => $options) {
            foreach ($ruleClasses as $ruleClass) {
                if ($ruleClass::getName() == $rule) {
                    $ruleClass::validate($this->field, $value, $options);
                }
            }
        }
        return true;
    }
}
