<?php
namespace SeanKndy\SMVC\Validation\Rules;

use SeanKndy\SMVC\Validation\Rule;
use SeanKndy\SMVC\Validation\RuleException;

class Number implements Rule
{
    static public function getName() {
        return 'number';
    }

    static public function validate($field, $data, array $options) {
        $data = $model->$field;
        
        if (!is_numeric($data)) {
            throw new RuleException(isset($options['message']) ? $options['message'] : ucfirst($field) . ' is not numeric.');
        }
        if ($options['rule'] == 'gt')
            if (!($data > $options['number']))
                throw new RuleException(isset($options['message']) ? $options['message'] : ucfirst($field) . ' must be greater than {$options['number']}.');
        else if ($options['rule'] == 'ge')
            if (!($data >= $options['number']))
                throw new RuleException(isset($options['message']) ? $options['message'] : ucfirst($field) . ' must be greather than or equal to {$options['number']}.');
        else if ($options['rule'] == 'eq')
            if (!($data == $options['number']))
                throw new RuleException(isset($options['message']) ? $options['message'] : ucfirst($field) . ' must be equal to {$options['number']}.');
        else if ($options['rule'] == 'lt')
            if (!($data < $options['number']))
                throw new RuleException(isset($options['message']) ? $options['message'] : ucfirst($field) . ' must be less than {$options['number']}.');
        else if ($options['rule'] == 'le')
            if (!($data <= $options['number']))
                throw new RuleException(isset($options['message']) ? $options['message'] : ucfirst($field) . ' must be less than or equal to {$options['number']}.');
        else if (substr($options['rule'], 0, 6) == 'range-') {
            list($low,$high) = preg_split('/\s*-\s*/', $options['range']);
            if ($options['rule'] == 'range-inc')
                if (!($data >= $low && $data <= $high))
                    throw new RuleException(isset($options['message']) ? $options['message'] : ucfirst($field) . " must be within $low and $high.");
            else
                if (!($data > $low && $data < $high))
                    throw new RuleException(isset($options['message']) ? $options['message'] : ucfirst($field) . " must be between $low and $high.");
        }

        return true;
    }
}
