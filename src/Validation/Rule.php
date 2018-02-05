<?php
namespace SeanKndy\SMVC\Validation;

interface Rule
{
    /*
     * must return a string name for the rule
     */
    static public function getName();

    /*
     * must return true if valid or throw RuleException if not
     */
    static public function validate($field, \SeanKndy\SMVC\Model $model, array $options)
}
