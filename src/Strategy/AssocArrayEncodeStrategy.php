<?php

namespace JsonEncoder\Strategy;

class AssocArrayEncodeStrategy implements JsonEncodeStrategy {
    /**
     * @var JsonEncodeStrategy[]
     */
    private $strategies = [];
    
    /**
     * @var array
     */
    private $fields;
    
    public function __construct(array $fields = []) {
        $this->fields = array_flip($fields);
    }
    
    /**
     * {inheritdoc}
     */
    public function append($field, JsonEncodeStrategy $strategy) {
        $this->strategies[$field] = $strategy;
    }
    
    /**
     * {inheritdoc}
     */
    public function serialize($value) {
        if (! is_array($value)) return [];
    
        $value1 = array_intersect_key($value, $this->fields);
        $value2 = array_reduce(
            array_keys($this->strategies),
            function (&$tmp, $f) use($value) {
                return isset($value[$f]) ? $tmp + [$f => $this->strategies[$f]->serialize($value[$f])] : $tmp;
            },
            []
        );

        return $value1 + $value2;
    }
}
