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
            array(
                'name' => 'file',
                'type' => 'file',
                'options' => array(
                    'label' => 'Upload File',
                ),
                'attribute' => array(
                    'multiple' => true,
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
                    'value' => 'Upload File(s)',
                ),
            )
        );
    }
}
