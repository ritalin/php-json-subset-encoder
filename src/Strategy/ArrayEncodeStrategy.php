<?php

namespace JsonEncoder\Strategy;

class ArrayEncodeStrategy implements JsonEncodeStrategy {
    /**
     * @var JsonEncodeStrategy
     */
    private $strategy;

    public function __construct(JsonEncodeStrategy $itemStrategy = null) {
        $this->strategy = $itemStrategy;
    }
    
    /**
     * {inheritdoc}
     */
    public function append($field, JsonEncodeStrategy $strategy) { }
    
    /**
     * {inheritdoc}
     */
    public function serialize($value) {
        if (! is_array($value)) return[];
        
        if (is_object(current($value))) {
            return array_map(
                function ($obj) {
                    return $this->strategy->serialize($obj);
                },
                $value
            );
        }
        else {
            return $value;
        }
    }
}
