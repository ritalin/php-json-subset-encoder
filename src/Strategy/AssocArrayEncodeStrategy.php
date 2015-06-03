<?php

namespace JsonEncoder\Strategy;

use JsonEncoder\FilterRule;

class AssocArrayEncodeStrategy implements JsonEncodeStrategy {
    /**
     * @var JsonEncodeStrategy[]
     */
    private $strategies = [];
    
    /**
     * @var FilterRule
     */
    private $rule;
    
    public function __construct(FilterRule $rule) {
        $this->rule = $rule;
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
    
        $value1 = $this->rule->intersectByKey($value);
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
