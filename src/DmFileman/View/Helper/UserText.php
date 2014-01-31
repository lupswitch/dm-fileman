<?php

namespace DmFileman\View\Helper;

use DmCommon\View\Helper\UserText as DmCommonUserText;

class UserText extends DmCommonUserText
{
    const DIRECTORY  = 'directory';
    const FILE       = 'file';

    const INDEX  = 'index';
    const UPDATE = 'update';
    const CREATE = 'create';

    const SINGULAR = 'singular';
    const PLURAL   = 'plural';

    const UPLOAD_SUCCESS   = 'upload_success';
    const UPLOAD_FAILURE   = 'upload_failure';
    const DELETE_SUCCESS   = 'delete_success';
    const DELETE_FAILURE   = 'delete_failure';
    const DELETE_FORBIDDEN = 'delete_forbidden';

    protected static $messageTypes = array(
        'upload_success'   => 'Uploading %s was successful.',
        'upload_failure'   => 'Uploading %s failed.',
        'delete_success'   => 'Deleting %s was successful.',
        'delete_failure'   => 'Deleting %s failed.',
        'delete_forbidden' => 'Deleting %s is not allowed.',
    );
}
