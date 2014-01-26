<?php

return array(
    'dm-fileman' => array(
        'filePathBase' => '/',
        'fileUrlBase'  => '',
        'filemanager' => array(
            'namespace'    => 'dm-fileman',
            'upload_dir'   => 'public/upload',
            'upload_path'  => '/upload',
        ),
        'thumbs' => array(
            'width'  => 128,
            'height' => 128,
        )
    ),
    'router' => array(
        'routes' => array(
            'filemanager' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/filemanager',
                    'defaults' => array(
                        'controller' => 'DmFileman\Controller\FileManagerController',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'list' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/list/:dir',
                            'defaults' => array(
                                'action' => 'list',
                            ),
                        ),
                    ),
                    'refresh' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/refresh/:dir',
                            'defaults' => array(
                                'action' => 'refresh',
                            ),
                        ),
                    ),
                    'create' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/create/:dir',
                            'defaults' => array(
                                'action' => 'create',
                            ),
                        ),
                    ),
                    'delete' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/delete/:dir',
                            'defaults' => array(
                                'action' => 'delete',
                            ),
                        ),
                    ),
                    'upload' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/upload/:dir',
                            'defaults' => array(
                                'action' => 'upload',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
