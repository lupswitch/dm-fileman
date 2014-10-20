<?php

namespace DmFileman\Form;

use Zend\Form\Element;
use Zend\InputFilter;
use DmCommon\Form\BaseForm;

/**
 * Class DeleteFileForm
 *
 * @package DmFileman\Form
 */
class DeleteFileForm extends BaseForm
{
    const NAME     = 'name';
    const SECURITY = 'security';

    public function build()
    {
        $this->setAttribute('role', 'form');

        $this->add(
            [
                'name'    => 'name',
                'type'    => 'text',
                'options' => [
                    'label' => 'File Name',
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
                    'value' => 'Delete File',
                ],
            ]
        );
    }
}
