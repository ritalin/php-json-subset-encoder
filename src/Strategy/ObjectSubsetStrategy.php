<?php

namespace JsonEncoder\Strategy;

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
    public function serialize($value) {
        if (! is_object($value)) return [];
    
        $evaluator = new ObjectFieldEvaluator($value);
        
        $value1 = $this->builtin !== null ? $this->builtin->serialize($value) : [];
        $value2 = array_reduce(
            array_keys($this->strategies),
            function (&$tmp, $f) use($evaluator) {
                list($valid, $v) = $evaluator->evaluateField($f);
                
                return $valid ? $tmp + [$f => $this->strategies[$f]->serialize($v)] : $tmp;
            },
            []
        );

        return $value1 + $value2;
    }
}
