<?php

namespace JsonEncoder\Strategy;

final class ObjectFieldEvaluator {
    /**
     * @object
     */
    private $obj;
    
    /**
     * @array
     */
    private $fields;
    
    /**
     * @var array
     */
    private $methods;
    
    public function __construct($obj) {
        $this->obj = $obj;
        
        $class = get_class($obj);
        $this->fields = get_class_vars($class);
        $this->methods = array_flip(get_class_methods($class));
    }
    
    /**
     * @param string name
     * @return boolean
     */
    public function hasField($name) {
        return array_key_exists($name, $this->fields);
    }
    
    /**
     * @param string name
     * @return boolean
     */
    public function hasGetter($name) {
        return array_key_exists($name, $this->methods);
    }
    
    public function evaluateField($field) {
        if ($this->hasField($field)) {
            return [true, $this->obj->{$field}];
        }
        else if ($this->hasGetter($field)) {
            return [true, $this->obj->{$field}()];
        }
        else if ($this->hasGetter($getter = $getter = 'get' . ucfirst($field))) {
            return [true, $this->obj->{$getter}()];
        }
        else {
            return [false, null];
        }
    }
    
    public function evaluate(array $fields) {
        return array_reduce(
            $fields,
            function (array &$tmp, $field) {
                list($valid, $value) = $this->evaluateField($field);
                
                return $valid ? $tmp + [$field => $value] : $tmp;
            },
            []
        );
    }
    
    public function listObjectFields() {
        return array_keys(array_reduce(
            array_keys($this->fields + $this->methods),
            function (array &$tmp, $m) {
                if ($m === '__construct') {
                    return $tmp;
                }
                else if (($p = strpos($m, 'get')) === 0) {
                    $m = lcfirst(substr($m, 3));
                }
                
                return $tmp + [$m => null];
            },
            []
        ));
    }
}
