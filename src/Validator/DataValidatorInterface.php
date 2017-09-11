<?php
namespace SeanKndy\SMVC\Validator;

interface DataValidatorInterface
{
    /*
     * This method should actually validate $var within 
     */
    public function validate(Model $model, $var, $rule);
    
    
    public static function name();
}
