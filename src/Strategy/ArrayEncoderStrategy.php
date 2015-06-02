<?php

namespace JsonEncoder\Strategy;

class ArrayEncoderStrategy implements JsonEncodeStrategy {
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
                    return new JsonSubsetObjectEncoder($obj, $fields);
                },
                $this->values
            );
        }
        else {
            return $value;
        }
    }
}
