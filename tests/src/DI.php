<?php

namespace DeimosTest;

class DI extends \Deimos\DI\DI
{

    /**
     * configure DI
     */
    protected function configure()
    {
        $this->value('hello', 'привет');
    }

}