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

namespace Seaf\Web;

use Seaf;
use Seaf\Core\Environment\Environment;
use Seaf\FrameWork as FW;

/**
 * FramwWork Front Controller
 */
class FrontController extends FW\FrontController
{
    public function init( )
    {
        parent::init();

        // Viewを使えるようにする
        $this->register('view','Seaf\View\View');

        $this->event()->on('before.dispatch-loop', array($this, 'beforeDispatchLoop'));
        $this->event()->on('after.dispatch-loop', array($this, 'afterDispatchLoop'));

        $this->bind( $this, array(
            'render' => '_render',
            'redirect' => '_redirect'
        ));
    }

    public function beforeDispatchLoop( )
    {
        ob_start();
    }

    public function afterDispatchLoop( )
    {
       $this->render(ob_get_clean());
    }

    /**
     * レンダリング
     */
    public function _render($body = '')
    {
        if ($this->has('view')) {
            $view_name = $this->get('view');
            $body = $this->view()->render($view_name);
        }

        $this->response()->write($body);

        $this->event()->trigger('before.output', $this->response());

        $this->response()->send();
    }

    /**
     * リダイレクト
     */
    public function _redirect( $uri, $code = 303 )
    {
        $this->response( )
            ->status($code)
            ->header('Location', $this->request()->getUriMask().'/'.ltrim($uri,'/'))
            ->send( );
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
