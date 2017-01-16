<?php

namespace Test;

use Deimos\DI\Argument;
use Deimos\DI\ContainerEmpty;
use Deimos\DI\Group;
use DeimosTest\DI;
use DeimosTest\Person;
use DeimosTest\TestSetUp;

class DITest extends TestSetUp
{

    public function testTest()
    {
        $this->assertEquals(
            4,
            $this->di->random()
        );
        $this->assertEquals(
            $this->di->get('math.getRandom.getRandom'),
            $this->di->random()
        );
        $this->assertEquals(
            $this->di->math()->getRandom()->getRandom(),
            $this->di->random()
        );
        $this->assertEquals(
            $this->di->math()->get('getRandom.getRandom'),
            $this->di->random()
        );
        $this->assertEquals(
            $this->di->math()->call('getRandom.getRandom', []),
            $this->di->random()
        );

        $this->assertEquals(
            81,
            $this->di->pow()
        );

        $this->assertEquals(
            25,
            $this->di->call('math.pow.mathClass.pow', [5, 2])
        );
    }

    public function testEmptyContainer()
    {
        $this->assertInstanceOf(ContainerEmpty::class, new ContainerEmpty());
    }

    public function testGroup()
    {
        $this->assertInstanceOf(Group::class, $this->di->get('math.pow'));
    }

    public function testCallback()
    {
        $rand1 = $this->di->call('random');
        $rand2 = $this->di->call('random');
        $rand3 = $this->di->call('random');

        $this->assertTrue($rand1 === $rand2 && $rand2 === $rand3);
    }

    public function testPerson()
    {
        $this->assertInstanceOf(Person::class, $this->di->get('ivan'));

        $this->di->call('ivan.setAge', ['@nine']);

        $this->assertEquals($this->di->nine(), 9);

        $this->assertEquals($this->di->get('ivan.age'), $this->di->nine());
        $this->assertEquals($this->di->get('ivan.age'), $this->di->get('nine'));

        $this->di->call('ivan.setAge', [34]);
        $this->assertEquals($this->di->get('ivan.age'), 34);

        $this->assertEquals($this->di->ivan()->age(), 34);
    }

    public function testTree()
    {
        // group
        $this->assertInstanceOf(Group::class, $this->di->get('l1'));
        $this->assertInstanceOf(Group::class, $this->di->get('l1.l2'));
        $this->assertInstanceOf(Group::class, $this->di->get('l1.l2.l3'));
        $this->assertInstanceOf(Group::class, $this->di->get('l1.l2.l3.l4'));
        $this->assertInstanceOf(Group::class, $this->di->get('l1.l2.l3.l4.l5'));

        // argument
        $this->assertInstanceOf(Argument::class, $this->di->get('l1.l2.l3.l4.l5.argument'));
        $this->assertCount(2, $this->di->call('l1.l2.l3.l4.l5.argument.get'));

        list($two, $nine) = $this->di->get('l1.l2.l3.l4.l5.argument.get');
        $this->assertNotEquals($two, $this->di->get('two'));
        $this->assertNotEquals($nine, $this->di->get('nine'));
    }

    public function testStatic()
    {
        $this->assertInstanceOf(Group::class, DI::l1());
    }

}
