<?php

namespace DmFileman\Form;

use Zend\Form\Element;
use Zend\InputFilter;
use DmCommon\Form\BaseForm;

/**
 * Class UploadFileForm
 *
 * @package DmFileman\Form
 *
 * @method \DmFileman\InputFilter\UploadFile getInputFilter
 */
class UploadFileForm extends BaseForm
{
    const FILE     = 'file';
    const SECURITY = 'security';

    public function build()
    {
        $this->setAttribute('role', 'form');

        $this->add(
            [
                'name'      => 'file',
                'type'      => 'file',
                'options'   => [
                    'label' => 'Upload File',
                ],
                'attribute' => [
                    'multiple' => true,
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
                    'value' => 'Upload File(s)',
                ],
            ]
        );
    }
}
