<?php

namespace Test\Target;

class PrivateClass {
    public $a;
    private $b;
    private $c;
    private $d;
    private $e;
    
    public function __construct($a, $b, $c, $d, $e) {
        $this->a = $a;
        $this->b = $b;
        $this->c = $c;
        $this->d = $d;
        $this->e = $e;
    }
    
    private function a() { return $this->a; }
    public function b() { return $this->b; }
    public function getD() { return $this->d; }
}
