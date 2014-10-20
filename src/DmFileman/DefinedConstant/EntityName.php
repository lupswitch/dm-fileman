<?php

namespace DmFileman\DefinedConstant;

use DmCommon\DefinedConstant\ConstantProviderInterface;
use DmCommon\View\Helper\UserText;

class EntityName implements ConstantProviderInterface
{
    const DIRECTORY = 'directory';
    const FILE      = 'file';

    /** @var array */
    protected static $messages = [
        UserText::SINGULAR => [
            self::DIRECTORY => 'Directory',
            self::FILE      => 'File',
        ],
        UserText::PLURAL   => [
            self::DIRECTORY => 'Directories',
            self::FILE      => 'Files',
        ],
    ];

    /**
     * @return array
     */
    public static function getConstants()
    {
        return array_keys(self::$messages);
    }

    /**
     * @return array|void
     */
    public static function getMessages()
    {
        return self::$messages;
    }
}
