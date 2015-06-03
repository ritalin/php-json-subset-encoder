<?php

namespace JsonEncoder\Strategy;

use JsonEncoder\FilterRule;
use JsonEncoder\Formatter\ObjectFormatable;

class ObjectToArrayStrategy implements JsonEncodeStrategy {
    /**
     * @var FilterRule
     */
    private $rule;
    
    public function __construct(FilterRule $rule) {
        $this->rule = $rule;
    }
    
    private static function defaultFormatters() {
        return [
            \DateTime::class => new \JsonEncoder\Formatter\DateTimeFormatter
        ];    
    }
    
    public function append($field, JsonEncodeStrategy $strategy) { }
    
    /**
     * {inheritdoc}
     */
    public function serialize($value, array $formatters) {
        if (! is_object($value)) return [];
        
        $evaluator = new ObjectFieldEvaluator($value);

        return $evaluator->evaluate(
            $this->rule->isFieldAllIncludes() ? $evaluator->listFields() : $this->rule->listIncludeFields(),
            $formatters
        );
    }
}
