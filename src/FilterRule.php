<?php

namespace JsonEncoder;

class FilterRule {
    public static function newRule(array $nestedFilters = []) {
        return new self($nestedFilters);
    }
    
    /**
     * @var FilterRule[]
     */
    public $nestedFilters = [];
    
    /**
     * @var string[]
     */
    private $fields = [];
    
    /**
     * @var string[]
     */
    private $excludeFields = [];
    
    /**
     * @var boolean
     */
    private $isObjectRule = true;
    
    /**
     * @var boolean
     */
    private $fieldAllIncludes = true;
    
    public function withArrayRule() {
        $this->isObjectRule = false;
        
        return $this;
    }
    
    /**
     * @param string[] fieldNames
     */
    public function includes(array $fieldNames) {
        $this->fields += array_flip($fieldNames);
        $this->fieldAllIncludes = false;
        
        return $this;
    }
    
    /**
     * @param string[] fieldNames
     */
    public function excludes(array $fieldNames) {
        $this->excludeFields += array_flip($fieldNames);
        $this->fieldAllIncludes = false;
        
        return $this;
    }
    
    /**
     * @param string fieldName
     * @param FilterRule rule
     */
    public function nestRule($fieldName, FilterRule $rule) {
        return $this->nestRules([$fieldName => $rule]);
    }
    
    /**
     * @param FilterRule[] rules
     */
    public function nestRules(array $rules) {
        $this->nestedFilters += $rules;
        $this->excludeFields += $rules;
        
        return $this;
    }
    
    /**
     * @return boolean
     */
    public function isObjectRule() {
        return $this->isObjectRule;
    }
    
    /**
     * @return boolean
     */
    public function isFieldAllIncludes() {
        return $this->fieldAllIncludes;
    }
    
    /**
     * @return string[]
     */
    public function listIncludeFields() {
        return array_keys(array_diff_key($this->fields, $this->excludeFields));
    }
    
    /**
     * @param mixed[] values
     * @return mixed[]
     */
    public function intersectByKey(array $values) {
        if (! $this->fieldAllIncludes) {
            $values = array_intersect_key($values, $this->fields);
        }
        
        return array_diff_key($values, $this->excludeFields);
    }
    
    protected function __construct() { }
}
