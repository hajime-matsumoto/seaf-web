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
use Seaf\Core\Environment\EnvironmentAcceptableIF;
use Seaf\Core\Environment\Environment;

/**
 * ビュー
 */
class View extends Environment implements EnvironmentAcceptableIF
{
    const DEFAULT_ENGINE = 'php';

    /**
     * ViewEngineネームスペース
     *
     * @var array
     */
    protected $view_engine_namespace_list = array(
        'Seaf\\Web\\Component\\View\\Engine'
    );

    /**
     * Viewパラメータ
     */
    protected $viewPrams = array();

    public function __construct ( ) 
    {
        parent::__construct();
        $this->viewParams = array();
    }

    public function param ($name, $value = false)
    {
        if (is_array($name)) {
            foreach ($name as $v => $k) {
                $this->param($name, $value);
            }
            return $this;
        }
        $this->viewParams[$name] = $value;
        return $this;
    }

    /**
     * エンジンの取得
     */
    public function getEngine( )
    {
        $engine = $this->get('engine', self::DEFAULT_ENGINE);

        foreach ($this->view_engine_namespace_list as $ns) {
            if (class_exists($class = $ns.'\\'.ucfirst($engine))) {
                $rc = new \ReflectionClass($class);
                $engine = $rc->newInstance($this);
            }
        }
        if (!is_object($engine)) {
            throw new EngineNotExist($engine);
        }

        return $engine;
    }

    /**
     * 親環境とConfigを共有する
     */
    public function acceptEnvironment (Environment $env) 
    {
        $this->register('config', function () use ($env) {
            return $env->di('config');
        });
    }

    /**
     * レンダリングする
     */
    public function render( $view_name, $params = array() )
    {
        $file = $this->config()->getf(
            "{{root.path}}/{{view.path}}/$view_name"
        );

        $params = array_merge($this->viewParams, $params);

        return $this->getEngine()->render($view_name, $params);
    }
}

class EngineNotExist extends \Exception 
{
    public function __construct ($name) 
    {
        parent::__construct(sprintf('Viewエンジン %s は存在しません。', $name));
    }
}
/* vim: set expandtab ts=4 sw=4 sts=4: et*/
