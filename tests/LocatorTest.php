<?php

namespace Sabre\ResourceLocator;

class LocatorTest extends \PHPUnit_Framework_TestCase {

    function testConstruct() {

        $locator = new Locator();
        $this->assertInstanceOf(
            NullResource::class,
            $locator->get('')
        );

    }

    /**
     * @depends testConstruct
     * @expectedException \Sabre\ResourceLocator\NotFoundException
     */
    function testGetNotFound() {

        $locator = new Locator();
        $locator->get('foo');

    }

    /**
     * @depends testGetNotFound
     */
    function testMountResource() {
        
        $locator = new Locator();
        $locator->mount('foo', new NullResource());
        $this->assertInstanceOf(
            NullResource::class,
            $locator->get('foo')
        );
        $this->assertEquals(
            [
                'child' => [
                    'foo',
                ]
            ],
            $locator->getLinks('')
        );
        $this->assertEquals(
            [
                'parent' => [
                    '',
                ]
            ],
            $locator->getLinks('foo')
        );

    }

    /**
     * @depends testGetNotFound
     */
    function testMountCallBack() {
        
        $locator = new Locator();
        $locator->mount('foo', function() { return new NullResource(); });
        $this->assertInstanceOf(
            NullResource::class,
            $locator->get('foo')
        );
        $this->assertEquals(
            [
                'child' => [
                    'foo',
                ]
            ],
            $locator->getLinks('')
        );

    }

    /**
     * @depends testConstruct
     * @expectedException InvalidArgumentException
     */
    function testMountInvalid() {

        $locator = new Locator();
        $locator->mount('foo', 'blabla');

    }

    /**
     * @depends testMountResource
     */
    function testLink() {
    
        $locator = new Locator();
        $locator->mount('foo', new NullResource());
        $locator->link('foo', 'homepage', 'http://evertpot.com/');
        $locator->link('foo', 'homepage', 'http://evertpot.com/');
        $this->assertEquals(
            [
                'parent' => [
                    '',
                ],
                'homepage' => [
                    'http://evertpot.com/',
                    'http://evertpot.com/',
                ]
            ],
            $locator->getLinks('foo')
        );

    }

    /**
     * @depends testMountResource
     */ 
    function testGetFromParentResource() {

        $parent = $this->getMock('Sabre\ResourceLocator\ParentResourceInterface');
        $parent->expects()->method('getChild')->once()->willReturn(function() { return new NullResource(); });

        $locator = new Locator();
        $locator->mount('parent', $parent);

        $locator->get('parent/child');

    }

}