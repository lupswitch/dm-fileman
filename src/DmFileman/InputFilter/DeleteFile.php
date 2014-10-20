<?php

namespace DmFileman\InputFilter;

use Zend\InputFilter\InputFilter;

class DeleteFile extends InputFilter
{
    public function init()
    {
        $this->add(
            [
                'name'       => 'name',
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 160,
                        ],
                    ],
                ],
            ]
        );

        $this->add(
            [
                'name'     => 'security',
                'required' => true,
                /* csrf is autoadded
                'validators' => array(
                    array(
                        'name'    => 'Csrf',
                        'options' => array(
                        ),
                    ),
                ),*/
            ]
        );
    }
}
