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

        $this->event()->on('before.dispatch-loop', array($this, '_beforeDispatchLoop'));
        $this->event()->on('after.dispatch-loop', array($this, '_afterDispatchLoop'));
    }

    public function _beforeDispatchLoop( )
    {
        ob_start();
    }

    public function _afterDispatchLoop( )
    {
       $this->render(ob_get_clean());
    }

    /**
     * レンダリング
     */
    public function render($body)
    {
        if ($this->has('view')) {
            $view_name = $this->get('view');

            $this
                ->response()
                ->write($this->view()->render($view_name))
                ->send();
        }
        echo $body;
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
