<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 25.10.2016
 * Time: 15:52
 */

namespace App\Validation;

use Respect\Validation\Exceptions\NestedValidationException;
use Slim\Http\Request;


class Validator
{
    use RulesList;
    public $errors;
    protected $c;


    public function __construct($container)
    {
        $this->c = $container;
    }

    public function validate(Request $req, array $rules)
    {
        foreach ($rules as $field => $rule) {
            try {
                $rule->setName(str_replace('_', ' ', ucfirst($field)))->assert($req->getParam($field, null));
            } catch (NestedValidationException $e) {
                $this->errors[$field] = $e->getMessages()[0];
            }
        }
        return $this;
    }


    public function failed()
    {
        return !empty($this->errors);
    }
}
