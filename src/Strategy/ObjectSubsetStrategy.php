<?php

namespace JsonEncoder\Strategy;

use JsonEncoder\Formatter\ObjectFormatable;

class ObjectSubsetStrategy implements JsonEncodeStrategy {
    /**
     * @var JsonEncodeStrategy[]
     */
    private $strategies = [];
    
    /**
     * @var JsonEncodeStrategy
     */
    private $builtin;
    
    public function __construct(JsonEncodeStrategy $strategy = null) {
        $this->builtin = $strategy;
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
    public function serialize($value, array $formatters) {
        if (! is_object($value)) return [];
    
        $evaluator = new ObjectFieldEvaluator($value);
        
        $value1 = $this->builtin !== null ? $this->builtin->serialize($value, $formatters) : [];
        $value2 = array_reduce(
            array_keys($this->strategies),
            function (&$tmp, $f) use($evaluator, $formatters) {
                list($valid, $v) = $evaluator->evaluateField($f);
                
                return $valid ? $tmp + [$f => $this->strategies[$f]->serialize($v, $formatters)] : $tmp;
            },
            []
        );

        return $value1 + $value2;
    }
}
