<?php

namespace JsonEncoder;

final class FilterRule {
    /**
     * @var string
     */
    public $isObjectRule = true;
    
    /**
     * @var string[]
     */
    public $fields;
    
    /**
     * @var FilterRule[]
     */
    public $nestedFilters;
    
    public static function newFilter(array $fields, array $nestedFilters = []) {
        return new self($fields, $nestedFilters);
    }
    
    public function withArrayRule() {
        $this->isObjectRule = false;
        
        return $this;
    }
    
    /**
     * @return boolean
     */
    public function isObjectRule() {
        return $this->isObjectRule;
    }
    
    private function __construct(array $fields, array $nestedFilters) {
        $this->fields = $fields;
        $this->nestedFilters = $nestedFilters;
    }
}
