<?php
namespace Cart\Filter;

use Zend\InputFilter\InputFilter;

class CartFilter extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'name'     => 'product_id',
            'required' => true,
            'filters'  => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim']
            ]
        ]);

        $this->add([
            'name'     => 'cart_id',
            'required' => false,
            'filters'  => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim']
            ]
        ]);

        $this->add([
            'name'     => 'customer_id',
            'required' => false,
            'filters'  => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim']
            ]
        ]);

        $this->add([
            'name'     => 'qty',
            'required' => true,
            'filters'  => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim']
            ]
        ]);
    }

    public function validateAndSanitizeInput($input)
    {
        $this->setData($input);
        $filteredArray = $this->getValues();

        return $filteredArray;
    }
}
