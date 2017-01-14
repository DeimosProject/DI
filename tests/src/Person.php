<?php

namespace DeimosTest;

class Person
{

    protected $firstName;
    protected $lastName;

    protected $age;

    public function __construct($firstName, $lastName)
    {
        $this->firstName = $firstName;
        $this->lastName  = $lastName;
    }

    public function setAge($value)
    {
        $this->age = $value;
    }

    public function name()
    {
        return $this->lastName . ' ' . $this->firstName;
    }

    public function age()
    {
        return $this->age;
    }

}