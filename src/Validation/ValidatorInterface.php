<?php
namespace SeanKndy\SMVC\Validation;

interface ValidatorInterface
{
    /*
     * This method should actually validate $var within 
     */
    public function validate(Model $model, $var, $rule);
    
    /*
     * This method should return the name of the validator
     */
    public static function name();
}
