<?php

namespace Test;

class JsonSubsetObjectEncoder implements \JsonSerializable {
    private $obj;
    private $fields;

    public function __construct($obj, array $fields) {
        $this->obj = $obj;
        $this->fields = $fields;
    }
    
    /**
     * {inheritdoc}
     */
    public function jsonSerialize() {
        $vars = get_class_vars(get_class($this->obj));
        $methods = array_flip(get_class_methods(get_class($this->obj)));
        
        return array_reduce(
            array_keys($this->fields),
            function (array &$tmp, $f) use($vars, $methods) {
                if (array_key_exists($f, $vars)) {
                    return $tmp + [$f => $this->toFieldEncoder($f, $this->obj->{$f})];
                }
                else if (array_key_exists($f, $methods)) {
                    return $tmp + [$f => $this->toFieldEncoder($f, $this->obj->{$f}())];
                }
                else if (array_key_exists(($getter = $getter = 'get' . ucfirst($f)), $methods)) {
                    return $tmp + [$f => $this->toFieldEncoder($f, $this->obj->{$getter}())];
                }
                else {
                    return $tmp;
                }
            },
            []
        );
    }
    
    private function toFieldEncoder($name, $value) {
        if (is_object($value)) {
            return new JsonSubsetObjectEncoder($value, $this->fields[$name]);
        }
        else {
            return $value;
        }
    }
}
