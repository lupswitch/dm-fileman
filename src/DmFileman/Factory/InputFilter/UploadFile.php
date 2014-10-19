<?php

namespace DmFileman\Factory\InputFilter;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use DmFileman\Helper\Options as OptionsHelper;
use DmFileman\InputFilter\UploadFile as UploadFileInputFilter;

class UploadFile implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return UploadFileInputFilter
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var OptionsHelper $optionsHelper */
        $optionsHelper = $serviceLocator->get('DmFileman\Helper\Options');

        $uploadFileFilter = new UploadFileInputFilter();

        $uploadFileFilter->setExtensions($optionsHelper->getExtensions());
        $uploadFileFilter->setMaxSize($optionsHelper->getMaxSize());

        return $uploadFileFilter;
    }
}
