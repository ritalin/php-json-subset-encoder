<?php

namespace JsonEncoder;

final class ObjectMeta {
    /**
     * @var string
     */
    public $class;
    
    /**
     * @var string[]
     */
    public $fields;
    
    /**
     * @var ObjectMeta[]
     */
    public $nestedMetas;
    
    public function __construct($class, array $fields, array $nestedMetas = []) {
        $this->class = $class;
        $this->fields = $fields;
        $this->nestedMetas = $nestedMetas;
    }
}
