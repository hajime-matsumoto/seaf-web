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
use Seaf\Web\FrontController;
use Seaf\Cli\Command;

/**
 * アセットマネージャー
 * ==========================
 */
class AssetManager extends FrontController
{
    private $parent_env;
    private $assets_path_list = array();
    private $ext_map = array(
        'css' => array(
            'suffix'=> array('sass','scss','css')
        ),
        'js' => array(
            'suffix'=> array('coffee','js')
        )
    );
    private $compile_map = array(
        'sass' => array(
            'cmd'=>'sass',
            'opt'=>'--compass --cache-location {{root.path}}/{{cache.path}} -s -E "{{encoding}}"'
        ),
        'coffee' => array(
            'cmd'=>'coffee',
            'opt'=>'-c -p -s'
        )
    );

    public function init ( )
    {
        parent::init( );
    }

    public function acceptEnvironment( Environment $env )
    {
        parent::acceptEnvironment($env);

        $this->parent_env = $env;
    }


    public function mount($uri)
    {
        $self = $this;

        $this->parent_env->router()->map(
            $uri.'(/@page:*)',
            function ($page, $req) use ($uri, $self) {
                $self->request( )->setUri($req->getUri());
                $self->request( )->setUriMask($uri);
                $self->run();
            }
        );

        $this->event()->on('notfound', function( ){
            $this->parent_env->event()->trigger('notfound', $this->parent_env);
        });


        return $this;
    }


    public function addAssetsPath ( $path )
    {
        array_unshift($this->assets_path_list, $path);
    }

    public function afterDispatchLoop()
    {
        $file = $this->getFile($uri = $this->request()->getUri());

        if ($file === false) {
            $this->event()->trigger('notfound',$this);
            Seaf::system()->halt();
        }

        $this
            ->response( )
            ->header('Content-Type', $this->contentType($uri))
            ->cache( 86400)
            ->sendHeaders();
        ob_end_clean();

        $this->compile($file);

        Seaf::system()->halt();
    }

    public function contentType ($file)
    {
        $ext = substr($file,strrpos($file,'.')+1);

        switch ($ext)
        {
        case 'js':
            return 'text/javascript';
        case 'css':
            return 'text/css';
        }

        return 'text/text';
    }

    public function compile ($file)
    {
        $ext = substr($file,strrpos($file,'.')+1);

        if (!isset($this->compile_map[$ext])) {
            return file_get_contents($file);
        }

        $map = $this->compile_map[$ext];

        $cmd = $map['cmd'];
        $opt = $this->config()->getf($map['opt']);

        if ( $cmd == 'sass' ) {
            $opt.= ' -I '.implode(' -I', $this->assets_path_list);
            $opt.= ' ';
        }

        $command = new Command($cmd,$opt);


        $command
            ->inputFile($file)
            ->execute()
            ->getStdOut($out)
            ->getStdError($err);

        $line = trim(fgets($err));

        if (strlen($line) > 0) {
            $err = $line.stream_get_contents($err);
            $this->response()->status(200)->write($err)->send();
        }

        while ($line = fgets($out)) {
            echo $line;
        }
    }

    public function getFile ($file)
    {
        $ext = substr($file,strrpos($file,'.')+1);
        $fileName = substr($file,0,strrpos($file,'.'));
        $map = $this->ext_map[$ext];
        //
        // ファイルを探す
        //
        foreach ($this->assets_path_list as $path) {

            foreach ($map['suffix'] as $suffix) {
                $file = $path.'/'.ltrim($fileName,'/').'.'.$suffix;
                if (file_exists($file)) return $file;
            }
        }
        return false;
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
