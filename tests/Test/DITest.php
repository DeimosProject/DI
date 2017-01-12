<?php

namespace Test;

use Deimos\DI\Group;
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
            $this->di->pow1()
        );
    }

}
