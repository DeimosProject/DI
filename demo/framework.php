<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

class Framework extends \Deimos\DI\DI
{

    protected function configure()
    {
        $this->value('project', 'Deimos');
        $this->value('version', 0.1);

        $this->callback('info', function ()
        {
            return implode(' ', [
                $this->project(),
                $this->version()
            ]);
        });

    }

}

$framework = new Framework();

echo Framework::info();

try
{
    echo \Deimos\DI\ContainerEmpty::info();
}
catch (Error $error) // php 7+
{
    var_dump($error);
}