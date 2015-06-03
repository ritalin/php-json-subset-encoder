<?php

namespace JsonEncoder\Strategy;

use JsonEncoder\Formatter\ObjectFormatable;

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
    public function serialize($value, array $formatters) {
        if (! is_array($value)) return[];
        
        if (is_object(current($value))) {
            if ($this->strategy != null) {
                return array_map(
                    function ($obj) use($formatters) {
                        return $this->strategy->serialize($obj, $formatters);
                    },
                    $value
                );
            }
            else {
                return array_map(
                    function ($obj) use($formatters) {
                        $class = get_class($obj);
                        if (! isset($formatters[$class])) {
                            throw new \Exception("formatter is not found (type: $class)");
                        }
                        
                        return $formatters[$class]->format($obj);
                    },
                    $value
                );
            }
        }
        
        return $value;
    }
}
