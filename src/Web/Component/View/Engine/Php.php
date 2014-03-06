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

namespace Seaf\Web\Component\View\Engine;

use Seaf;
use Seaf\Core\Environment\EnvironmentAcceptableIF;
use Seaf\Core\Environment\Environment;
use Seaf\Web\Component\View;

/**
 * ビューエンジン
 */
class Php extends Environment
{
    /**
     */
    private $view;

    public function __construct (View $view) 
    {
        $this->view = $view;
    }

    public function getTemplateFileName ($name)
    {
        $path = $this->view->config()->getf('{{root.path}}/{{view.path}}');
        return $path.'/'.$name.'.php';
    }

    public function render ($name, $params = array())
    {
        $file = $this->getTemplateFileName($name);
        if (!file_exists($file)) {
            throw new FileDoseNotExists($file);
        }

        $view = $this;

        $renderFunction = function() use ($view, $file, $params) {
            foreach ($params as $k => $v) {
                $$k = $v;
            }
            ob_start();
            include $file;
            $data = ob_get_clean();
            return $data;
        };

        return $renderFunction();
    }
}

class FileDoseNotExists extends \Exception
{
    public function __construct ($file) 
    {
        parent::__construct(sprintf('%sは存在しません。',$file));
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
