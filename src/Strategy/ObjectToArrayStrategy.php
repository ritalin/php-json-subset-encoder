<?php

namespace JsonEncoder\Strategy;

use JsonEncoder\FilterRule;

class ObjectToArrayStrategy implements JsonEncodeStrategy {
    /**
     * @var FilterRule
     */
    private $rule;
    
    public function __construct(FilterRule $rule) {
        $this->rule = $rule;
    }
    
    public function append($field, JsonEncodeStrategy $strategy) { }
    
    /**
     * {inheritdoc}
     */
    public function serialize($value) {
        if (! is_object($value)) return [];
        
        $evaluator = new ObjectFieldEvaluator($value);

        return $evaluator->evaluate(
            $this->rule->isFieldAllIncludes() ? $evaluator->listFields() : $this->rule->listIncludeFields()
        );
    }
}
