<?php
namespace SeanKndy\SMVC\Validation\Rules;

use SeanKndy\SMVC\Validation\Rule;
use SeanKndy\SMVC\Validation\RuleException;

class String implements Rule
{
    static public function getName() {
        return 'string';
    }

    static public function validate($field, \SeanKndy\SMVC\Model $model, array $options) {
        $data = $model->$field;
        if ($options['rule'] == 'not-empty') {
            if (trim($data) == '')
                throw new RuleException(isset($options['message']) ? $options['message'] : ucfirst($field) . ' cannot be empty.');
        } else if ($options['rule'] == 'equals') {
            if ($model->get($name) != $options['equal-to'])
                throw new RuleException(isset($options['message']) ? $options['message'] : ucfirst($field) . ' must equal {$options['equal-to']}.');
        } else if ($options['rule'] == 'email') {
            if (!filter_var($model->get($name), FILTER_VALIDATE_EMAIL))
                throw new RuleException(isset($options['message']) ? $options['message'] : ucfirst($field) . ' must be an email address.');
        } else if ($options['rule'] == 'length-ge') {
            if (!(strlen($model->get($name)) >= $options['length']))
                throw new RuleException(isset($options['message']) ? $options['message'] : ucfirst($field) . ' must be more than ' . ($options['length'] - 1) . " characters.");
        }
        return true;
    }
}
