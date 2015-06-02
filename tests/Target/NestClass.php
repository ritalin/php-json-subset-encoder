<?php

namespace Test\Target;

class NestClass {
    public $a;
    public $b;
    public $obj;
    
    public function __construct($a, $b, $obj) {
        $this->a = $a;
        $this->b = $b;
        $this->obj = $obj;
    }
}
