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
        $meta = FilterRule::newFilter(['c', 'd']);

        $builder = EncoderBuilder::ofObject($meta);
        
        $this->assertInstanceOf(EncoderBuilder::class, $builder);
        $this->assertInstanceOf(Strategy\ObjectToArrayStrategy::class, $builder->strategy());
        
        $obj = new Target\PublicClass(function($o) {
            $o->a = 123;
            $o->b = 'xyz';
            $o->c = '@@@';
            $o->d = 999;
        });
        
        $result = $builder->strategy()->serialize($obj);
        
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
    public function test_build_as_nested_object() {
        $meta = FilterRule::newFilter(['b'], [
            'obj' => FilterRule::newFilter(['b', 'c', 'd'])
        ]);
        
        $builder = EncoderBuilder::ofObject($meta);
        
        $this->assertInstanceOf(EncoderBuilder::class, $builder);
        $this->assertInstanceOf(Strategy\ObjectSubsetStrategy::class, $builder->strategy());
        
        $obj = new Target\NestClass(666, 'ghqazjk', new Target\PrivateClass(345, 'bbb', 'oop', 'ggg', '123'));

        $result = $builder->strategy()->serialize($obj);
        
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

        $result = $builder->strategy()->serialize([11, 'xyz', 101, 987]);
        
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
        $meta = FilterRule::newFilter(['c', 'd']);

        $builder = EncoderBuilder::ofObjectArray($meta);
        
        $this->assertInstanceOf(EncoderBuilder::class, $builder);
        $this->assertInstanceOf(Strategy\ArrayEncodeStrategy::class, $builder->strategy());
        
        $obj = new Target\PublicClass(function($o) {
            $o->a = 123;
            $o->b = 'xyz';
            $o->c = '@@@';
            $o->d = 999;
        });
        
        $values = [$obj, $obj, $obj];
        
        $result = $builder->strategy()->serialize($values);
        
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
        $meta = FilterRule::newFilter(['b'], [
            'obj' => FilterRule::newFilter(['b', 'c', 'd'])
        ]);

        $builder = EncoderBuilder::ofObjectArray($meta);
        
        $this->assertInstanceOf(EncoderBuilder::class, $builder);
        $this->assertInstanceOf(Strategy\ArrayEncodeStrategy::class, $builder->strategy());

        $obj = new Target\NestClass(666, 'ghqazjk', new Target\PrivateClass(345, 'bbb', 'oop', 'ggg', '123'));
        $values = [$obj, $obj, $obj, $obj];

        $result = $builder->strategy()->serialize($values);

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
        $meta = FilterRule::newFilter(['a'], [
            'c' => FilterRule::newFilter(['b', 'c'], [
                'd' => FilterRule::newFilter(['x', 'y'])->withArrayRule()
            ])
        ])->withArrayRule();

        $builder = EncoderBuilder::ofAssocArray($meta);
        
        $this->assertInstanceOf(EncoderBuilder::class, $builder);
        $this->assertInstanceOf(Strategy\AssocArrayEncodeStrategy::class, $builder->strategy());

        $values = ['a' => 666, 'b' => 'ghqazjk', 'c' => new Target\PrivateClass(345, 'bbb', 'oop', ['x' => 100, 'y' => 200], '123')];

        $result = $builder->strategy()->serialize($values);

        $this->assertEquals(
            [ 'a' => 666, 'c' => [ 'b' => 'bbb', 'd' => ['x' => 100, 'y' => 200], ] ],
            $result
        );
    }
}
