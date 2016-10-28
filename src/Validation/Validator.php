<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 25.10.2016
 * Time: 15:52
 */

namespace App\Validation;

use Slim\Http\Request;
use Respect\Validation\Exceptions\NestedValidationException;


class Validator {
    public $errors;
    protected $c;

    function __construct($container){
        $this->c = $container;
    }

    function validate(Request $req, array $rules){
        foreach ($rules as $field => $rule) {
            try {
                $rule->setName(str_replace('_', ' ', ucfirst($field)))->assert($req->getParam($field, null));
            } catch (NestedValidationException $e) {
                $this->errors[$field] = $e->getMessages()[0];
            }
        }
        return $this;
    }


    function failed(){
        return !empty($this->errors);
    }
}

