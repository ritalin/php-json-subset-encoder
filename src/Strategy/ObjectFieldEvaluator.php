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
    
    public function evaluate($field) {
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
}
