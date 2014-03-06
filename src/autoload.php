<?php
/**
 * Seaf Auto Load
 */
Seaf::di('autoLoader')->addNamespace(
    'Seaf\\Web',
    null,
    dirname(__FILE__).'/Web'
);

Seaf::register('web', 'Seaf\Web\FrontController');
