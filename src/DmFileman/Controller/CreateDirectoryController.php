<?php

namespace DmFileman\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use DmCommon\Controller\TestableControllerTrait;
use DmCommon\View\Helper\UserText;
use DmCommon\DefinedConstant\Message;
use DmFileman\Service\FileManager\FileManager;
use DmFileman\Form\CreateDirectoryForm;
use DmFileman\Service\Thumbnailer\Thumbnailer;
use DmFileman\DefinedConstant\EntityName;

/**
 * Class CreateDirectoryController
 *
 * @package DmFileman\Controller
 *
 * @method ViewModel layout(string $template = null)
 */
class CreateDirectoryController extends AbstractActionController
{
    use CurrentPathTrait;
    use TestableControllerTrait;

    /** @var FileManager */
    private $fileManager;

    /** @var CreateDirectoryForm */
    private $createDirForm;

    /** @var Thumbnailer */
    private $thumbnailer;

    /** @var bool */
    private $initialized = false;

    /** @var UserText */
    private $userText;

    /**
     * @param FileManager           $fileManager
     * @param CreateDirectoryForm   $createDirectoryForm
     * @param Thumbnailer           $thumbnailer
     * @param UserText              $userText
     */
    public function __construct(
        FileManager $fileManager,
        CreateDirectoryForm $createDirectoryForm,
        Thumbnailer $thumbnailer,
        UserText $userText
    ) {
        $this->fileManager = $fileManager;

        $this->createDirForm = $createDirectoryForm;

        $this->thumbnailer = $thumbnailer;

        $this->userText = $userText;
    }

    /**
     * @return FileManager
     */
    private function getFileManager()
    {
        if (!$this->initialized) {
            $this->fileManager->setCurrentPath($this->getCurrentPath());
        }

        return $this->fileManager;
    }

    /**
     * @return \Zend\Http\Response
     */
    public function createAction()
    {
        if ($this->handleCreatePost($this->createDirForm)) {
            $msg = $this->userText->getMessage(EntityName::DIRECTORY, Message::CREATE_SUCCESS);
            $this->flashMessenger()->addSuccessMessage($msg);
        } else {
            $msg = $this->userText->getMessage(EntityName::DIRECTORY, Message::CREATE_FAILURE);
            $this->flashMessenger()->addErrorMessage($msg);
        }

        return $this->redirect()->toRoute('filemanager/list', array('dir' => $this->getCurrentPath()));
    }

    /**
     * @param CreateDirectoryForm $form
     *
     * @return bool
     */
    private function handleCreatePost(CreateDirectoryForm $form)
    {
        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            $form->getInputFilter()->init();

            if ($form->isValid()) {
                return $this->getFileManager()->create($form->getData()['directoryName']);
            }
        }

        return false;
    }
}
