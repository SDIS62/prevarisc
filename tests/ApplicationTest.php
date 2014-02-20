<?php

class ApplicationTest extends PHPUnit_Framework_TestCase
{
    public function testTest()
    {
        $etablissement_service = new Service_Etablissement(new Model_DbTable_Etablissement);
    }
}
