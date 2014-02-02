<?php

namespace DmFileman\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use DmCommon\Controller\TestableControllerTrait;
use DmCommon\DefinedConstant\Message;
use DmCommon\View\Helper\UserText;
use DmFileman\Service\FileManager\FileManager;
use DmFileman\Form\DeleteFileForm;
use DmFileman\Service\Thumbnailer\Thumbnailer;
use DmFileman\DefinedConstant\EntityName;

/**
 * Class DeleteFileController
 *
 * @package DmFileman\Controller
 *
 * @method ViewModel layout(string $template = null)
 */
class DeleteFileController extends AbstractActionController
{
    use CurrentPathTrait;
    use TestableControllerTrait;

    /** @var FileManager */
    private $fileManager;

    /** @var DeleteFileForm */
    private $deleteFileForm;

    /** @var Thumbnailer */
    private $thumbnailer;

    /** @var bool */
    private $initialized = false;

    /** @var UserText */
    private $userText;

    /**
     * @param FileManager           $fileManager
     * @param DeleteFileForm        $deleteFileForm
     * @param Thumbnailer           $thumbnailer
     * @param UserText              $userText
     */
    public function __construct(
        FileManager $fileManager,
        DeleteFileForm $deleteFileForm,
        Thumbnailer $thumbnailer,
        UserText $userText
    ) {
        $this->fileManager = $fileManager;

        $this->deleteFileForm = $deleteFileForm;

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
    public function deleteAction()
    {
        $this->deleteFileForm->build();

        if ($this->handleDeletePost($this->deleteFileForm)) {
            $this->flashMessenger()
                ->addSuccessMessage($this->userText->getMessage(EntityName::FILE, Message::DELETE_SUCCESS));
        } else {
            $this->flashMessenger()
                ->addErrorMessage($this->userText->getMessage(EntityName::FILE, Message::DELETE_FAILURE));
        }

        return $this->redirect()->toRoute('filemanager/list', array('dir' => $this->getCurrentPath()));
    }

    /**
     * @param DeleteFileForm $form
     *
     * @return bool
     */
    private function handleDeletePost(DeleteFileForm $form)
    {
        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            $form->getInputFilter()->init();

            if ($form->isValid()) {
                return $this->getFileManager()->delete($form->getData()['name']);
            }
        }

        return false;
    }
}
