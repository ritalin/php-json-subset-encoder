<?php

namespace Test;

use JsonEncoder\ObjectMeta;
use JsonEncoder\EncoderBuilder;

use JsonEncoder\Strategy;
use JsonEncoder\Serializer;

use Test\Target;

class EncodeBuilderTest extends \PHPUnit_Framework_TestCase {
    /**
     * @test
     */
    public function test_build_as_object() {
        $meta = new ObjectMeta(Target\PublicClass::class, ['c', 'd']);

        $builder = EncoderBuilder::AsObject($meta);
        
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
        $meta = new ObjectMeta(Target\NestClass::class, ['b'], [
            'obj' => new ObjectMeta(Target\PrivateClass::class, ['b', 'c', 'd'])
        ]);
        
        $builder = EncoderBuilder::AsObject($meta);
        
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
}
