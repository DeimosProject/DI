<?php

namespace DeimosTest;

class DI extends \Deimos\DI\DI
{

    /**
     * configure DI
     */
    protected function configure()
    {

        $this->value('two', 2);
        $this->value('nine', 9);

        $this->group('math', function ()
        {
            $this->instance('getRandom', Get4::class, []);

            $this->group('pow', function ()
            {
                $this->instance('mathClass', Math::class, []);
            });
        });

        $this->addBuildCallback('random', function ()
        {
            return $this->call('math.getRandom.getRandom', []);
        });

        $this->callback('pow', function ()
        {
            return $this->call('math.pow.mathClass.pow', ['@nine', '@two']);
        });
    }

}