<?php

namespace Bundle\DownloadBundle\Tests;

use Bundle\DownloadBundle\Resources\MediaType;

class MediaTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testGetType()
    {
        $this->assertEquals('application/pdf', MediaType::getType('pdf'));
        $this->assertEquals('application/octet-stream', MediaType::getType('nowaythisisanextension'));
    }
}

