<?php

namespace DmFileman\Factory\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use DmCommon\DefinedConstant\Action;
use DmFileman\DefinedConstant\EntityName;
use DmCommon\DefinedConstant\Message;
use DmCommon\View\Helper\UserText as UserTextHelper;

class UserText implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return UserTextHelper
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $userText = new UserTextHelper;

        $userText->addActions(Action::getMessages());
        $userText->addMessages(Message::getMessages());
        $userText->addMessages(Message::getMessages());
        $userText->addEntityNames(EntityName::getMessages());

        return $userText;
    }
}
