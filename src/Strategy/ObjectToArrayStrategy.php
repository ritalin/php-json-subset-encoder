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

        if ($this->rule->isFieldAllIncludes()) {
            return $evaluator->evaluateAll();
        }
        else {
            return array_reduce(
                $this->rule->listIncludeFields(),
                function (array &$tmp, $f) use($evaluator) {
                    list($valid, $value) = $evaluator->evaluate($f);
                    
                    return $valid ? $tmp + [$f => $value] : $tmp;
                },
                []
            );
        }
    }
}
