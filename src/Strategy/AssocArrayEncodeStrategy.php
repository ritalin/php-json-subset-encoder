<?php

namespace JsonEncoder\Strategy;

use JsonEncoder\FilterRule;
use JsonEncoder\Formatter\ObjectFormatable;

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
    public function serialize($value, array $formatters) {
        if (! is_array($value)) return [];
    
        $value1 = $this->rule->intersectByKey($value);
        $value1 = array_reduce(
            array_keys($value1),
            function (array &$tmp, $k) use($value1, $formatters) {
                $v = $value1[$k];
                if (is_object($v)) {
                    $class = get_class($v);
                    if (! isset($formatters[$class])) {
                        throw new \Exception("formatter is not found (type: $class)");
                    }
                    $v = $formatters[$class]->format($v);
                }
                return $tmp + [$k => $v];
            },
            []
        );
        
        $value2 = array_reduce(
            array_keys($this->strategies),
            function (&$tmp, $f) use($value, $formatters) {
                return isset($value[$f]) ? $tmp + [$f => $this->strategies[$f]->serialize($value[$f], $formatters)] : $tmp;
            },
            []
        );

        return $value1 + $value2;
    }
}
