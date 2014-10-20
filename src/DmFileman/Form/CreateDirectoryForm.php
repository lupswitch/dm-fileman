<?php

namespace DmFileman\Form;

use Zend\Form\Element;
use DmCommon\Form\BaseForm;

/**
 * Class CreateDirectoryForm
 *
 * @package DmFileman\Form
 */
class CreateDirectoryForm extends BaseForm
{
    const DIRECTORY = 'directoryName';
    const SECURITY  = 'security';

    public function build()
    {
        $this->setAttribute('role', 'form');

        $this->add(
            [
                'name'    => 'directoryName',
                'type'    => 'text',
                'options' => [
                    'label' => 'Directory Name',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'security',
                'type' => 'csrf',
            ]
        );

        $this->add(
            [
                'name'       => 'submit',
                'type'       => 'submit',
                'attributes' => [
                    'value' => 'Create Directory',
                ],
            ]
        );
    }
}
