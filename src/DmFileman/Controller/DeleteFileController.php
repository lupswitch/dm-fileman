<?php

namespace DmFileman\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use DmFileman\Service\FileManager\FileManager;
use DmFileman\Form\DeleteFileForm;
use DmFileman\View\Helper\UserText;
use DmFileman\Service\Thumbnailer\Thumbnailer;

/**
 * Class DeleteFileController
 *
 * @package DmFileman\Controller
 *
 * @method ViewModel layout(string $template = null)
 */
class DeleteFileController extends AbstractActionController
{
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
     * @return string
     */
    private function getCurrentPath()
    {
        $currentPath = $this->params('dir');

        $currentPath = $currentPath ? urldecode($currentPath) : '/';

        return $currentPath;
    }

    /**
     * @return string
     */
    private function getOrigDir()
    {
        return $this->getFileManager()->getOrigDir($this->getCurrentPath());
    }

    /**
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        $this->deleteFileForm->build();

        if ($this->handleDeletePost($this->deleteFileForm)) {
            $this->flashMessenger()
                ->addSuccessMessage($this->userText->getMessage(UserText::FILE, UserText::DELETE_SUCCESS));
        } else {
            $this->flashMessenger()
                ->addErrorMessage($this->userText->getMessage(UserText::FILE, UserText::DELETE_FAILURE));
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
