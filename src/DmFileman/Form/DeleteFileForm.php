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
            array(
                'name' => 'name',
                'type' => 'text',
                'options' => array(
                    'label' => 'File Name',
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
                    'value' => 'Delete File',
                ),
            )
        );
    }
}
