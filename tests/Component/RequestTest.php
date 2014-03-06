<?php
namespace Seaf\Web\Component;

use Seaf;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    protected $req;

    public function setup ( ) {
        $this->req = Seaf::web()->request();
    }

    public function testRequestTest()
    {
        $req = Seaf::web()->request();

        $this->assertInstanceOf(
            __NAMESPACE__.'\\Request',
            $req
        );
    }
}
