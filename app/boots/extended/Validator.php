<?php

use Symfony\Component\Translation\TranslatorInterface;

class CustomValidator extends Illuminate\Validation\Validator {

    /**
     * Create a new Validator instance.
     *
     * @param  \Symfony\Component\Translation\TranslatorInterface  $translator
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @return void
     */
    public function __construct(TranslatorInterface $translator, $data, $rules, $messages = array())
    {
        parent::__construct($translator, $data, $rules, $messages);

        $this->implicitRules = array_merge($this->implicitRules, array('IfElse'));
    }

    /**
     * Validate if else condition.
     *
     * Ex. 'field' => 'if_else:(another_field=ok),required+numeric+min:3,required+integer+max:10'
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  mixed  $parameters
     * @return boolean
     */
    public function validateIfElse($attribute, $value, $parameters)
    {
        list ($condition, $then, $else) = $parameters;

        preg_match('/^\((\w+)(.*?)(\w+)\)/', $condition, $matches);

        if ( ! isset($matches[3])) return falae;

        $bool = false;

        switch (trim($matches[2]))
        {
            case '>'  :
                $bool = ($matches[3] > array_get($this->data, $matches[1]));
                break;
            case '<'  :
                $bool = ($matches[3] < array_get($this->data, $matches[1]));
                break;
            case '>=' :
                $bool = ($matches[3] >= array_get($this->data, $matches[1]));
                break;
            case '<=' :
                $bool = ($matches[3] <= array_get($this->data, $matches[1]));
                break;
            case '!=' :
            case '<>' :
                $bool = ($matches[3] != array_get($this->data, $matches[1]));
                break;
            default   :
                $bool = ($matches[3] == array_get($this->data, $matches[1]));
                break;
        }

        $rules = $bool ? $then : $else;
        $rules = explode('+', $rules);

        $this->rules[$attribute] += $rules;

        if (count($rules)) foreach ($rules as $rule)
        {
            $this->validate($attribute, $rule);
        }

        return true;
    }

}

/**
 * Resolve custom validator.
 */
Validator::resolver(function($translator, $data, $rules, $messages)
{
    return new CustomValidator($translator, $data, $rules, $messages);
});