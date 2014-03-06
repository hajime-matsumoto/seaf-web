<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * クラスを定義する
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Web\Component;

use Seaf;
use Seaf\Core\Environment\Environment;
use Seaf\FrameWork as FW;

/**
 * リクエストクラス
 */
class Request extends FW\Component\Request
{
    public function init ( )
    {
        parent::init();

        $this->setUriMask(dirname($_SERVER['SCRIPT_NAME']));

        $uri = $_SERVER['REQUEST_URI'];
        /*
        Array
        (
            [scheme] => http
            [host] => hostname
            [user] => username
            [pass] => password
            [path] => /path
            [query] => arg=value
            [fragment] => anchor
        )
        */
        $parts = parse_url($uri);

        $this->setUri($parts['path']);
        if (isset($parts['query'])) {
            parse_str($parts['query'], $params);
            $this->set($params);
        }

        // メソッドの設定
        $this->setMethod(
            Seaf::util()->arrayGet(
                $_SERVER, 'HTTP_X_HTTP_METHOD_OVERRIDE', Seaf::util()->arrayGet(
                    $_REQUEST, '_method', Seaf::util()->arrayGet(
                        $_SERVER, 'REQUEST_METHOD', 'GET'
                    )
                )
            )
        );
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
