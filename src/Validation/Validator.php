<?php
namespace SeanKndy\SMVC\Validation;

class Validator
{
    protected $model;
    protected $fields = [];
    protected $rules = [
        Rules\String::class,
        Rules\Number::class
    ];

    public function __construct(\SeanKndy\SMVC\Model $model) {
        $this->model = $model;
    }

    public function require($field) {
        $validation = $this->getValidation($field);
        $validation->setRequired(true);
        return $this;
    }

    public function allowEmpty($field) {
        $validation = $this->getValidation($field);
        $validation->setAllowEmpty(true);
        return $this;
    }

    public function rule($field, $rule, array $options = []) {
        $validation = $this->getValidation($field);
        $validation->addRule($rule, $options);
        return $this;
    }

    public function getValidations() {
        return $this->fields;
    }

    public function validate(array $onlyFields = []) {
        if (!$onlyFields) {
            $onlyFields = array_keys($this->fields);
        }
        $errors = [];
        foreach ($this->fields as $field => $validation) {
            if (!isset($onlyFields[$field])) continue;
            if (!isset($this->model->$field) && $validation->isRequired()) {
                $errors[] = $field . ' is required, but missing.';
                continue;
            }
            try {
                $validation->validate($this->rules, $this->model->$field);
            } catch (RuleException $e) {
                $errors[] = $e->getMessage();
            }
        }
    }

    protected function getValidation($field) {
        if (!isset($this->fields[$field])) {
            $this->fields[$field] = new Validation($field);
        }
        return $this->fields[$field];
    }
}
