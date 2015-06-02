<?php

namespace JsonEncoder\Strategy;

class ObjectToArrayStrategy implements JsonEncodeStrategy {
    /**
     * @var string[]
     */
    private $fields;
    
    public function __construct(array $fields) {
        $this->fields = $fields;
    }
    
    public function append($field, JsonEncodeStrategy $strategy) { }
    
    /**
     * {inheritdoc}
     */
    public function serialize($value) {
        if (! is_object($value)) return [];
        
        $evaluator = new ObjectFieldEvaluator($value);
        
        return array_reduce(
            $this->fields,
            function (array &$tmp, $f) use($evaluator) {
                list($valid, $value) = $evaluator->evaluate($f);
                
                return $valid ? $tmp + [$f => $value] : $tmp;
            },
            []
        );
    }
}
