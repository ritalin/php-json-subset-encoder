<?php

namespace Test;

use JsonEncoder\FilterRule;
use JsonEncoder\EncoderBuilder;

use JsonEncoder\Strategy;
use JsonEncoder\Serializer;

use Test\Target;

class EncodeBuilderTest extends \PHPUnit_Framework_TestCase {
    /**
     * @test
     */
    public function test_build_as_object() {
        $rule = 
            FilterRule::newRule()
            ->includes(['c', 'd'])
        ;

        $builder = EncoderBuilder::ofObject($rule);
        
        $this->assertInstanceOf(EncoderBuilder::class, $builder);
        $this->assertInstanceOf(Strategy\ObjectToArrayStrategy::class, $builder->strategy());
        
        $obj = new Target\PublicClass(function($o) {
            $o->a = 123;
            $o->b = 'xyz';
            $o->c = '@@@';
            $o->d = 999;
        });
        
        $result = $builder->strategy()->serialize($obj, $builder->formatters());
        
        $this->assertEquals(
            [
                'c' => '@@@',
                'd' => 999
            ],
            $result
        );
        
        $serializer = $builder->build($obj);
        
        $this->assertInstanceOf(Serializer\JsonEncodeSerializer::class, $serializer);
        
        $result2 = $serializer->jsonSerialize();
        
        $this->assertEquals(
            [
                'c' => '@@@',
                'd' => 999
            ],
            $result2
        );
        
        $result3 = json_decode(json_encode($serializer), true);
        
        $this->assertEquals(
            [
                'c' => '@@@',
                'd' => 999
            ],
            $result3
        );
    }
    /**
     * @test
     */
    public function test_build_as_datetime() {
        $rule = 
            FilterRule::newRule()
            ->includes(['a'])
        ;
        $builder = EncoderBuilder::ofObject($rule);

        $obj = new Target\PublicClass(function($o) { 
            $o->a = new \DateTime('2015/12/13 14:15:36', new \DateTimeZone('Asia/Tokyo')); 
        });
         
        $result = $builder->strategy()->serialize($obj, $builder->formatters());
        
        $this->assertEquals(
            [
                'a' => '2015-12-13T14:15:36+0900',
            ],
            $result
        );
        
        $serializer = $builder->build($obj);
        $result2 = $serializer->jsonSerialize();
        
        $this->assertEquals(
            [
                'a' => '2015-12-13T14:15:36+0900',
            ],
            $result2
        );
        
        $result3 = json_decode(json_encode($serializer), true);
        
        $this->assertEquals(
            [
                'a' => '2015-12-13T14:15:36+0900',
            ],
            $result3
        );
    }
    
    /**
     * @test
     */
    public function test_build_as_nested_object() {
        $rule = 
            FilterRule::newRule()
            ->includes(['b'])
            ->nestRule('obj', 
                FilterRule::newRule()
                ->includes(['b', 'c', 'd'])
            )
        ;
        
        $builder = EncoderBuilder::ofObject($rule);
        
        $this->assertInstanceOf(EncoderBuilder::class, $builder);
        $this->assertInstanceOf(Strategy\ObjectSubsetStrategy::class, $builder->strategy());
        
        $obj = new Target\NestClass(666, 'ghqazjk', new Target\PrivateClass(345, 'bbb', 'oop', 'ggg', '123'));

        $result = $builder->strategy()->serialize($obj, $builder->formatters());
        
        $this->assertEquals(
            [
                'b' => 'ghqazjk',
                'obj' => [
                    'b' => 'bbb',
                    'd' => 'ggg',
                ]
            ],
            $result
        );
        
        $serializer = $builder->build($obj);
        
        $result2 = $serializer->jsonSerialize();
        
        $this->assertEquals(
            [
                'b' => 'ghqazjk',
                'obj' => [
                    'b' => 'bbb',
                    'd' => 'ggg',
                ]
            ],
            $result2
        );
        
        $result3 = json_decode(json_encode($serializer), true);
        
        $this->assertEquals(
            [
                'b' => 'ghqazjk',
                'obj' => [
                    'b' => 'bbb',
                    'd' => 'ggg',
                ]
            ],
            $result3
        );
    }
    
    /**
     * @test
     */
    public function test_build_as_primitive_array() {
        $builder = EncoderBuilder::ofPrimitiveArray();
        
        $this->assertInstanceOf(EncoderBuilder::class, $builder);
        $this->assertInstanceOf(Strategy\ArrayEncodeStrategy::class, $builder->strategy());

        $result = $builder->strategy()->serialize([11, 'xyz', 101, 987], $builder->formatters());
        
        $this->assertEquals([11, 'xyz', 101, 987], $result);

        $serializer = $builder->build([11, 'xyz', 101, 987]);
        $result2 = $serializer->jsonSerialize();
        
        $this->assertEquals([11, 'xyz', 101, 987], $result2);
        
        $result3 = json_decode(json_encode($serializer), true);

        $this->assertEquals([11, 'xyz', 101, 987], $result3);
        
    }
    
    /**
     * @test
     */
    public function test_build_as_object_array() {
        $rule = 
            FilterRule::newRule()
            ->includes(['c', 'd'])
        ;

        $builder = EncoderBuilder::ofObjectArray($rule);
        
        $this->assertInstanceOf(EncoderBuilder::class, $builder);
        $this->assertInstanceOf(Strategy\ArrayEncodeStrategy::class, $builder->strategy());
        
        $obj = new Target\PublicClass(function($o) {
            $o->a = 123;
            $o->b = 'xyz';
            $o->c = '@@@';
            $o->d = 999;
        });
        
        $values = [$obj, $obj, $obj];
        
        $result = $builder->strategy()->serialize($values, $builder->formatters());
        
        $this->assertEquals(
            [
                ['c' => '@@@', 'd' => 999],
                ['c' => '@@@', 'd' => 999],
                ['c' => '@@@', 'd' => 999],
            ],
            $result
        );
    
        $serializer = $builder->build($values);
        
        $result2 = $serializer->jsonSerialize();
        
        $this->assertEquals(
            [
                ['c' => '@@@', 'd' => 999],
                ['c' => '@@@', 'd' => 999],
                ['c' => '@@@', 'd' => 999],
            ],
            $result2
        );
        
        $result3 = json_decode(json_encode($serializer), true);
        
        $this->assertEquals(
            [
                ['c' => '@@@', 'd' => 999],
                ['c' => '@@@', 'd' => 999],
                ['c' => '@@@', 'd' => 999],
            ],
            $result3
        );
    }
    
    /**
     * @test
     */
    public function test_build_as_nested_object_array() {
        $rule = 
            FilterRule::newRule()
            ->includes(['b'])
            ->nestRule('obj',
                FilterRule::newRule()
                ->includes(['b', 'c', 'd'])
            )
        ;

        $builder = EncoderBuilder::ofObjectArray($rule);
        
        $this->assertInstanceOf(EncoderBuilder::class, $builder);
        $this->assertInstanceOf(Strategy\ArrayEncodeStrategy::class, $builder->strategy());

        $obj = new Target\NestClass(666, 'ghqazjk', new Target\PrivateClass(345, 'bbb', 'oop', 'ggg', '123'));
        $values = [$obj, $obj, $obj, $obj];

        $result = $builder->strategy()->serialize($values, $builder->formatters());

        $this->assertEquals(
            [
                 [ 'b' => 'ghqazjk', 'obj' => [ 'b' => 'bbb', 'd' => 'ggg', ] ],
                 [ 'b' => 'ghqazjk', 'obj' => [ 'b' => 'bbb', 'd' => 'ggg', ] ],
                 [ 'b' => 'ghqazjk', 'obj' => [ 'b' => 'bbb', 'd' => 'ggg', ] ],
                 [ 'b' => 'ghqazjk', 'obj' => [ 'b' => 'bbb', 'd' => 'ggg', ] ],
            ],
            $result
        );
    
        $serializer = $builder->build($values);
        
        $result2 = $serializer->jsonSerialize();
        
        $this->assertEquals(
            [
                 [ 'b' => 'ghqazjk', 'obj' => [ 'b' => 'bbb', 'd' => 'ggg', ] ],
                 [ 'b' => 'ghqazjk', 'obj' => [ 'b' => 'bbb', 'd' => 'ggg', ] ],
                 [ 'b' => 'ghqazjk', 'obj' => [ 'b' => 'bbb', 'd' => 'ggg', ] ],
                 [ 'b' => 'ghqazjk', 'obj' => [ 'b' => 'bbb', 'd' => 'ggg', ] ],
            ],
            $result2
        );
    
        $result3 = json_decode(json_encode($serializer), true);
        
        $this->assertEquals(
            [
                 [ 'b' => 'ghqazjk', 'obj' => [ 'b' => 'bbb', 'd' => 'ggg', ] ],
                 [ 'b' => 'ghqazjk', 'obj' => [ 'b' => 'bbb', 'd' => 'ggg', ] ],
                 [ 'b' => 'ghqazjk', 'obj' => [ 'b' => 'bbb', 'd' => 'ggg', ] ],
                 [ 'b' => 'ghqazjk', 'obj' => [ 'b' => 'bbb', 'd' => 'ggg', ] ],
            ],
            $result3
        );
    }
    
    /**
     * @test
     */
    public function test_build_as_assoc_array() {
        $rule = 
            FilterRule::newRule()->withArrayRule()
            ->includes(['a'])
            ->nestRule('c', 
                FilterRule::newRule()
                ->includes(['b', 'c'])
                ->nestRule('d', 
                    FilterRule::newRule()->withArrayRule()
                    ->includes(['x', 'y'])
                )
            )
        ;

        $builder = EncoderBuilder::ofAssocArray($rule);
        
        $this->assertInstanceOf(EncoderBuilder::class, $builder);
        $this->assertInstanceOf(Strategy\AssocArrayEncodeStrategy::class, $builder->strategy());

        $values = ['a' => 666, 'b' => 'ghqazjk', 'c' => new Target\PrivateClass(345, 'bbb', 'oop', ['x' => 100, 'y' => 200], '123')];

        $result = $builder->strategy()->serialize($values, $builder->formatters());

        $this->assertEquals(
            [ 'a' => 666, 'c' => [ 'b' => 'bbb', 'd' => ['x' => 100, 'y' => 200], ] ],
            $result
        );
    }
}
