<?php

namespace Test;

class JsonSubsetEncoderTest extends \PHPUnit_Framework_TestCase {
    /**
     * @test
     */
    public function test_object_subsetting() {
        $obj = new PublicClass(10, 'aaa');
        
        $encoder = new JsonSubsetObjectEncoder($obj, ['text' => []]);
        
        var_dump(json_encode($encoder));
        
        $obj = new SomeClass(43, 'qwerty');
        
        $encoder = new JsonSubsetObjectEncoder($obj, ['text' => []]);
        
        var_dump(json_encode($encoder));
        
        $obj = new NestClass(999, new SomeClass(11, 'zzz'));
        
        $encoder = new JsonSubsetObjectEncoder($obj, ['id' => [], 'obj' => ['text' => []]]);
        
        var_dump(json_encode($encoder));
        
        $values = [10, 20, 30];
        $encoder = new JsonArrayEncoder($values, []);
        
        var_dump(json_encode($encoder));
        
        $objs = [
            new NestClass(11, new SomeClass(11, 'zzz')),
            new NestClass(100, new SomeClass(21, 'qwerty')),
            new NestClass(999, new SomeClass(31, 'text')),
        ];
        
        $encoder = new JsonArrayEncoder($objs, ['id' => [], 'obj' => ['text' => []]]);
        
        var_dump(json_encode($encoder));
    }
}
