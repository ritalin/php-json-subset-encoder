<?php

namespace Test;

class NestClass {
    public $id;
    public $obj;
    
    public function __construct($id, $obj) {
        $this->id = $id;
        $this->obj = $obj;
    }
}
