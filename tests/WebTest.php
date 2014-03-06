<?php
namespace Seaf\Web;

use Seaf;

class WebTest extends \PHPUnit_Framework_TestCase
{
    protected $req;

    public function setup ( ) {
        Seaf::web()->init();
        $this->web = Seaf::web();
    }

    /**
     * Router, Request, RouterがWeb\\Componentのものか確認する
     */
    public function testComponentsTest()
    {
        $req = Seaf::web()->request();
        $this->assertInstanceOf(__NAMESPACE__.'\\Component\\Request', $req);

        $req = Seaf::web()->response();
        $this->assertInstanceOf(__NAMESPACE__.'\\Component\\Response', $req);

        $req = Seaf::web()->router();
        $this->assertInstanceOf(__NAMESPACE__.'\\Component\\Router', $req);
    }

    /**
     * 基本的なWEBページ表示
     */
    public function testWebPage( )
    {
        $web = Seaf::web();

        // ルーティングを設定
        $web->event()->on('before.dispatch-loop', function ($web) {
            echo "\n";
            echo "<html>\n";
            echo "  <body>\n";
        });
        $web->event()->on('after.dispatch-loop', function ($web) {
            echo "  </body>\n";
            echo "</html>\n";
        });
        $web->router()->map('GET /', function ($req, $res, $web) {
            echo "    <h2>Hello World</h2>\n";
        });

        // リクエストを設定
        $web->request( )->setUri('/');
        $web->request( )->setMethod('GET');

        // 実行する
        ob_start();
        $web->run();
        $result = ob_get_clean();

        $this->assertEquals(60,strlen($result));
        ob_start();
    }

    /**
     * NOTFOUNDの設定
     */
    public function testNotFound( )
    {
        $web = Seaf::web();

        // ルーティングを設定
        $web->event()->on('notfound', function ($web) {
            echo '<h1>404 notfound</h1>';
        });

        // 実行する
        ob_start();
        $web->run();
        $result = ob_get_clean();

        $this->assertEquals($result, '<h1>404 notfound</h1>');
        ob_start();
    }

    /**
     * ルーティングがループのテスト
     */
    public function testLoop( )
    {
        $web = Seaf::web();

        // ルーティングを設定
        $web->router()->map('/', function ($req, $res, $web) {
            $res->param('flg1',true);
            return true;
        });
        $web->router()->map('/', function ($req, $res, $web) {
            $res->param('flg2',true);
        });

        // 実行する
        $web->run();

        $array = $web->response()->toArray();
        $this->assertTrue($array['params']['flg1']);
        $this->assertTrue($array['params']['flg2']);
    }
}
