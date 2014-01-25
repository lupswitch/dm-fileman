<?php

namespace DmFileman\InputFilter;

use Zend\InputFilter\InputFilter;

class DeleteFile extends InputFilter
{
    public function init()
    {
        $this->add(
            array(
                'name'       => 'name',
                'required'   => true,
                'filters'    => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 160,
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'name'       => 'security',
                'required'   => true,
                /* csrf is autoadded
                'validators' => array(
                    array(
                        'name'    => 'Csrf',
                        'options' => array(
                        ),
                    ),
                ),*/
            )
        );
    }
}
