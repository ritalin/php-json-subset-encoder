<?php

namespace Test;

use Test\Target;
use JsonEncoder\Strategy;

class JsonEncoderStrategyTest extends \PHPUnit_Framework_TestCase {
    /**
     * @test
     */
    public function test_public_object_to_array_strategy() {
        $strategy = new Strategy\ObjectToArrayStrategy(['b', 'a']);
        
        $obj = new Target\PublicClass;
        $obj->a = 10;
        $obj->b = 55;
        $obj->c = 1024;
        $obj->d = 99;
        
        $result = $strategy->serialize($obj);
        
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('a', $result);
        $this->assertEquals(10, $result['a']);
        $this->assertArrayHasKey('b', $result);
        $this->assertEquals(55, $result['b']);
    }
    
    /**
     * @test
     */
    public function test_evaluate_object_field() {
        $obj = new Target\PrivateClass('aa', 'bb', 999, 12345, 'xyz');
        
        $evaluator = new Strategy\ObjectFieldEvaluator($obj);
        
        $this->assertTrue($evaluator->hasField('a'));
        $this->assertFalse($evaluator->hasField('b'));
        $this->assertFalse($evaluator->hasField('c'));
        $this->assertFalse($evaluator->hasField('d'));
        $this->assertFalse($evaluator->hasField('e'));
        
        $this->assertFalse($evaluator->hasGetter('a'));
        $this->assertTrue($evaluator->hasGetter('b'));
        $this->assertFalse($evaluator->hasGetter('c'));
        $this->assertFalse($evaluator->hasGetter('d'));
        $this->assertFalse($evaluator->hasGetter('e'));
        
        $this->assertFalse($evaluator->hasGetter('getA'));
        $this->assertFalse($evaluator->hasGetter('getB'));
        $this->assertFalse($evaluator->hasGetter('getC'));
        $this->assertTrue($evaluator->hasGetter('getD'));
        $this->assertFalse($evaluator->hasGetter('getE'));
        
        $this->assertEquals([true, 'aa'], $evaluator->evaluate('a'));
        $this->assertEquals([true, 'bb'], $evaluator->evaluate('b'));
        $this->assertEquals([false, null], $evaluator->evaluate('c'));
        $this->assertEquals([true, 12345], $evaluator->evaluate('d'));
        $this->assertEquals([false, null], $evaluator->evaluate('e'));
    }
    
    /**
     * @test
     */
    public function test_private_object_to_array_strategy() {
        $strategy = new Strategy\ObjectToArrayStrategy(['b', 'a', 'd']);
        
        $obj = new Target\PrivateClass('aa', 'bb', 999, 12345, 'xyz');
        
        $result = $strategy->serialize($obj);
        
        $this->assertCount(3, $result);
        $this->assertArrayHasKey('a', $result);
        $this->assertEquals('aa', $result['a']);
        $this->assertArrayHasKey('b', $result);
        $this->assertEquals('bb', $result['b']);
        $this->assertArrayHasKey('d', $result);
        $this->assertEquals(12345, $result['d']);
    }
    
    /**
     * @test
     */
    public function test_object_subset_strategy() {
        $strategy = new Strategy\ObjectSubsetStrategy(new Strategy\ObjectToArrayStrategy(['a']));
        $strategy->append(
            'obj', new Strategy\ObjectSubsetStrategy(new Strategy\ObjectToArrayStrategy(['a', 'b', 'c']))
        );
        
        $obj = new Target\NestClass(
            'qwerty',
            9876,
            new Target\PublicClass(function ($o) {
                $o->a = 'aa';
                $o->b = 'bb';
                $o->c = 999;
                $o->d = 12345;
            })
        );
        
        $result = $strategy->serialize($obj);
        
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('a', $result);
        $this->assertEquals('qwerty', $result['a']);
        $this->assertArrayHasKey('obj', $result);
        $this->assertCount(3, $result['obj']);
        $this->assertArrayHasKey('a', $result['obj']);
        $this->assertEquals('aa', $result['obj']['a']);
        $this->assertArrayHasKey('b', $result['obj']);
        $this->assertEquals('bb', $result['obj']['b']);
        $this->assertArrayHasKey('c', $result['obj']);
        $this->assertEquals('999', $result['obj']['c']);
    }
    
    /**
     * @test
     */
    public function test_primitive_array_strategy() {
        $strategy = new Strategy\ArrayEncodeStrategy;
        
        $result = $strategy->serialize([1, 1, 2, 3, 5]);
        $this->assertEquals([1, 1, 2, 3, 5], $result);
        
        $result = $strategy->serialize(['aa', 'bb', 'ac', 'dd', 'ee']);
        $this->assertEquals(['aa', 'bb', 'ac', 'dd', 'ee'], $result);
        
        $result = $strategy->serialize(['aa', 'bb', 'ac', new Target\PublicClass, 1234]);
        $this->assertEquals(['aa', 'bb', 'ac', new Target\PublicClass, 1234], $result);
    }
    
    /**
     * @test
     */
    public function test_nested_object_array_strategy() {
        $objStrategy = new Strategy\ObjectSubsetStrategy(new Strategy\ObjectToArrayStrategy(['a']));
        $objStrategy->append(
            'obj', new Strategy\ObjectSubsetStrategy(new Strategy\ObjectToArrayStrategy(['a', 'b', 'c']))
        );

        $strategy = new Strategy\ArrayEncodeStrategy($objStrategy);
        
        $values = [
            new Target\NestClass(777, 'ghjk', new Target\PrivateClass('aa', 'bb', 999, 12345, 'xyz')),
            new Target\NestClass(666, 'ghqazjk', new Target\PrivateClass(345, 'bbb', 'oop', 'ggg', '123')),
            new Target\NestClass('okm', 555, new Target\PrivateClass('aa2', 'bcd', 987, '@@@', 'high')),
        ];
        
        $result = $strategy->serialize($values);
        
        $this->assertEquals(
            [
                [ 'a' => 777,
                'obj' => [
                    'a' => 'aa', 'b' => 'bb'
                ]],
                [ 'a' => 666,
                'obj' => [
                    'a' => 345, 'b' => 'bbb'
                ]],
                [ 'a' => 'okm',
                'obj' => [
                    'a' => 'aa2', 'b' => 'bcd'
                ]],
            ], 
            $result
        );
    }
    
    /**
     * @test
     */
    public function test_assoc_array_strategy() {
        $strategy = new Strategy\AssocArrayEncodeStrategy(['a']);
        $strategy->append(
            'b', new Strategy\ObjectSubsetStrategy(new Strategy\ObjectToArrayStrategy(['a', 'b', 'd'])) 
        );
        
        $values = [ 
            'a' => 777, 
            'b' => new Target\PrivateClass('aa', 'bb', 999, 12345, 'xyz'), 
            'c' => 'ghjk',
        ];
        
        $result = $strategy->serialize($values);
        
        $this->assertEquals(
            [
                'a' => 777,
                'b' => [ 'a' => 'aa', 'b' => 'bb', 'd' => 12345 ],
            ], 
            $result
        );
    }
}
