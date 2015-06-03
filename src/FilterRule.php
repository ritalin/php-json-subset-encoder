<?php

namespace JsonEncoder;

class FilterRule {
    public static function newRule() {
        return new self();
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
     * @return FilterRule
     */
    public function includes(array $fieldNames) {
        $this->fields += array_flip($fieldNames);
        $this->fieldAllIncludes = false;
        
        return $this;
    }
    
    /**
     * @param string className
     * @return FilterRule
     */
    public function includeProperties($className) {
        $this->fields += array_flip($fieldNames);
        $this->fieldAllIncludes = false;
        
        return $this;
    }
    
    /**
     * @param string[] fieldNames
     * @return FilterRule
     */
    public function excludes(array $fieldNames) {
        $this->excludeFields += array_flip($fieldNames);
        
        return $this;
    }
    
    /**
     * @param string fieldName
     * @param FilterRule rule
     * @return FilterRule
     */
    public function nestRule($fieldName, FilterRule $rule) {
        return $this->nestRules([$fieldName => $rule]);
    }
    
    /**
     * @param FilterRule[] rules
     * @return FilterRule
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
