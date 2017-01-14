<?php

namespace DeimosTest;

use Deimos\DI\Argument;

class DI extends \Deimos\DI\DI
{

    /**
     * configure DI
     */
    protected function configure()
    {

        $this->value('two', 2);
        $this->value('nine', 9);

        $this->build('firstName', function ()
        {
            return 'Ivan';
        });

        $this->value('lastName', 'Ivanov');

        $this->group('math', function ()
        {
            $this->instance('getRandom', Get4::class, []);

            $this->group('pow', function ()
            {
                $this->instance('mathClass', Math::class, []);
            });
        });

        $this->callback('random', function ()
        {
            return $this->call('math.getRandom.getRandom', []);
        });

        $this->value('pow', function ()
        {
            return $this->call('math.pow.mathClass.pow', ['@nine', '@two']);
        });

        $this->instance('ivan', Person::class, ['@firstName', '@lastName']);

        $this->group('l1', function ()
        {
            $this->group('l2', function ()
            {
                $this->group('l3', function ()
                {
                    $this->group('l4', function ()
                    {
                        $this->group('l5', function ()
                        {
                            $this->value('two', mt_rand(3, 9999)); // for debug
                            $this->value('nine', mt_rand(10, 9999));

                            $this->instance(
                                'argument',
                                Argument::class,
                                [$this, [$this->two(), $this->nine()]]
                            );
                        });
                    });
                });
            });
        });
    }

}