<?php

namespace Test\Target;

class PublicClass {
    public $a;
    public $b;
    public $c;
    public $d;
    
    public function __construct(callable $fn = null) {
        if (isset($fn)) {
            $fn($this);
        }
    }
}
