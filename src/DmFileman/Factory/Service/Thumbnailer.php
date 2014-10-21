<?php

namespace DmFileman\Factory\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use DmFileman\Helper\Options as OptionsHelper;
use DmFileman\Service\Thumbnailer\Factory as FactoryThumbnailer;
use DmFileman\Service\Thumbnailer\Thumbnailer as ThumbnailerService;

use Imagine\Gd\Imagine;

class Thumbnailer implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ThumbnailerService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var OptionsHelper $config */
        $options = $serviceLocator->get('DmFileman\Helper\Options');

        /** @var Imagine $imagine */
        $imagine = $serviceLocator->get('Imagine\Gd\Imagine');

        $factory = new FactoryThumbnailer();

        $thumbsOptions = $options->getThumbsOptions();

        $thumbnailer = new ThumbnailerService($imagine, $factory, $thumbsOptions);

        return $thumbnailer;
    }
}
