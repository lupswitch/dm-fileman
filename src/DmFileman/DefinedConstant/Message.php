<?php

namespace DmFileman\DefinedConstant;

use DmCommon\DefinedConstant\ConstantProviderInterface;

class Message implements ConstantProviderInterface
{
    const UPLOAD_SUCCESS   = 'upload_success';
    const UPLOAD_FAILURE   = 'upload_failure';
    const DELETE_SUCCESS   = 'delete_success';
    const DELETE_FAILURE   = 'delete_failure';
    const DELETE_FORBIDDEN = 'delete_forbidden';

    /** @var array */
    protected static $messages = [
        'upload_success'   => 'Uploading %s was successful.',
        'upload_failure'   => 'Uploading %s failed.',
        'delete_success'   => 'Deleting %s was successful.',
        'delete_failure'   => 'Deleting %s failed.',
        'delete_forbidden' => 'Deleting %s is not allowed.',
    ];

    /**
     * @return array
     */
    public static function getConstants()
    {
        return array_keys(self::$messages);
    }

    /**
     * @return array
     */
    public static function getMessages()
    {
        return self::$messages;
    }
}
