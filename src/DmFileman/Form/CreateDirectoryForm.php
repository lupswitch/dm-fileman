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
            array(
                'name' => 'directoryName',
                'type' => 'text',
                'options' => array(
                    'label' => 'Directory Name',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'security',
                'type' => 'csrf',
            )
        );

        $this->add(
            array(
                'name' => 'submit',
                'type' => 'submit',
                'attributes' => array(
                    'value' => 'Create Directory',
                ),
            )
        );
    }
}
