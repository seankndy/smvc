<?php
namespace SeanKndy\SMVC\Validation\Rules;

use SeanKndy\SMVC\Validation\Rule;
use SeanKndy\SMVC\Validation\RuleException;

class Custom implements Rule
{
    static public function getName() {
        return 'custom';
    }

    static public function validate($field, \SeanKndy\SMVC\Model $model, array $options) {
        $func = $options['func'];
        return $func($field, $model);
    }
}
