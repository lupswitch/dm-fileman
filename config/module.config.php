<?php

return [
    'dm-fileman'    => [
        'filemanager' => [
            'namespace'   => 'dm-fileman',
            'upload_dir'  => './public/upload',
            'upload_path' => '/upload',
        ],
        'thumbs'      => [
            'width'  => 128,
            'height' => 128,
        ],
        'file_upload' => [
            'max_size'   => 20480000,
            'extensions' => ['jpg', 'png', 'gif'],
        ],
    ],
    'guards'        => [
        [
            'type'    => 'Regexp',
            'options' => [
                'regexp'    => '(/filemanager/?.*)',
                'assertion' => 'RouteGuard\Assertion\Zf2Authentication\IsLoggedIn',
            ],
        ],
    ],
    'router'        => [
        'routes' => [
            'filemanager' => [
                'type'          => 'literal',
                'options'       => [
                    'route'    => '/filemanager',
                    'defaults' => [
                        'controller' => 'DmFileman\Controller\ListController',
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'list'    => [
                        'type'    => 'segment',
                        'options' => [
                            'route'    => '/list/:dir',
                            'defaults' => [
                                'action' => 'list',
                            ],
                        ],
                    ],
                    'refresh' => [
                        'type'    => 'segment',
                        'options' => [
                            'route'    => '/refresh/:dir',
                            'defaults' => [
                                'action' => 'refresh',
                            ],
                        ],
                    ],
                    'create'  => [
                        'type'    => 'segment',
                        'options' => [
                            'route'    => '/create/:dir',
                            'defaults' => [
                                'controller' => 'DmFileman\Controller\CreateDirectoryController',
                                'action'     => 'create',
                            ],
                        ],
                    ],
                    'delete'  => [
                        'type'    => 'segment',
                        'options' => [
                            'route'    => '/delete/:dir',
                            'defaults' => [
                                'controller' => 'DmFileman\Controller\DeleteFileController',
                                'action'     => 'delete',
                            ],
                        ],
                    ],
                    'upload'  => [
                        'type'    => 'segment',
                        'options' => [
                            'route'    => '/upload/:dir',
                            'defaults' => [
                                'controller' => 'DmFileman\Controller\UploadFileController',
                                'action'     => 'upload',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'view_manager'  => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'asset_manager' => [
        'resolver_configs' => [
            'paths' => [
                __DIR__ . '/../public',
            ],
        ],
    ],
];
