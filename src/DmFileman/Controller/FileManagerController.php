<?php

namespace DmFileman\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use DmFileman\Service\FileManager\FileManager;
use DmFileman\Form\CreateDirectoryForm;
use DmFileman\Form\DeleteFileForm;
use DmFileman\Form\UploadFileForm;
use DmFileman\View\Helper\UserText;
use DmFileman\Service\Thumbnailer\Thumbnailer;

/**
 * Class FileManagerController
 *
 * @package DmFileman\Controller
 *
 * @method ViewModel layout(string $template = null)
 */
class FileManagerController extends AbstractActionController
{
    /** @var FileManager */
    private $fileManager;

    /** @var CreateDirectoryForm */
    private $createDirForm;

    /** @var UploadFileForm */
    private $uploadFileForm;

    /** @var DeleteFileForm */
    private $deleteFileForm;

    /** @var Thumbnailer */
    private $thumbnailer;

    /** @var bool */
    private $initialized = false;

    /** @var array */
    private $mimeTypes = array(
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    );

    /** @var UserText */
    private $userText;

    /**
     * @param FileManager           $fileManager
     * @param CreateDirectoryForm   $createDirectoryForm
     * @param UploadFileForm        $uploadFileForm
     * @param DeleteFileForm        $deleteFileForm
     * @param Thumbnailer           $thumbnailer
     * @param UserText              $userText
     */
    public function __construct(
        FileManager $fileManager,
        CreateDirectoryForm $createDirectoryForm,
        UploadFileForm $uploadFileForm,
        DeleteFileForm $deleteFileForm,
        Thumbnailer $thumbnailer,
        UserText $userText
    ) {
        $this->fileManager = $fileManager;

        $this->createDirForm = $createDirectoryForm;

        $this->uploadFileForm = $uploadFileForm;

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
    public function getOrigDir()
    {
        return $this->getFileManager()->getOrigDir($this->getCurrentPath());
    }

    /**
     * @return \Zend\Http\Response
     */
    public function indexAction()
    {
        return $this->redirect()->toRoute('filemanager/list', array('dir' => '/'));
    }

    /**
     * @return ViewModel
     */
    public function listAction()
    {
        $this->createDirForm->build();

        $this->uploadFileForm->build();

        $this->deleteFileForm->build();

        $viewData = array(
            'list'       => $this->getFileManager()->getList(),
            'currentDir' => $this->getCurrentPath(),
            'createForm' => $this->createDirForm,
            'uploadForm' => $this->uploadFileForm,
            'deleteForm' => $this->deleteFileForm,
        );

        $this->layout('layout/filemanager.phtml');

        return new ViewModel($viewData);
    }

    /**
     * @return \Zend\Http\Response
     */
    public function refreshAction()
    {
        $this->getFileManager()->refresh();

        return $this->redirect()->toRoute('filemanager/list', array('dir' => $this->getCurrentPath()));
    }

    /**
     * @return \Zend\Http\Response
     */
    public function createAction()
    {
        if ($this->handleCreatePost($this->createDirForm)) {
            $this->flashMessenger()
                ->addSuccessMessage($this->userText->getMessage(UserText::DIRECTORY, UserText::CREATE_SUCCESS));
        } else {
            $this->flashMessenger()
                ->addErrorMessage($this->userText->getMessage(UserText::DIRECTORY, UserText::CREATE_FAILURE));
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

    /**
     * @return \Zend\Http\Response
     */
    public function updateAction()
    {
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

    /**
     * @return \Zend\Http\Response
     */
    public function uploadAction()
    {
        $this->uploadFileForm->build($this->getOrigDir());

        if ($this->handleUploadPost($this->uploadFileForm)) {
            $this->flashMessenger()
                ->addSuccessMessage($this->userText->getMessage(UserText::FILE, UserText::UPLOAD_SUCCESS));
        } else {
            $this->flashMessenger()
                ->addErrorMessage($this->userText->getMessage(UserText::FILE, UserText::UPLOAD_FAILURE));

            foreach ($this->uploadFileForm->getMessages() as $messages) {
                foreach ($messages as $message) {
                    $this->flashMessenger()
                        ->addErrorMessage($message);
                }
            }
        }

        return $this->redirect()->toRoute('filemanager/list', array('dir' => $this->getCurrentPath()));
    }

    /**
     * @param UploadFileForm $form
     *
     * @return bool
     */
    private function handleUploadPost(UploadFileForm $form)
    {
        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            // Make certain to merge the files info!
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $form->setData($post);

            $currentDir = $this->getFileManager()->getOrigDir($this->getCurrentPath());

            $form->getInputFilter()->setCurrentDir($currentDir)->init();

            if ($form->isValid()) {
                return $this->resizeImage($form->getData()['file']);
            }
        }

        return false;
    }

    /**
     * @param array $fileData
     *
     * @return bool
     */
    private function resizeImage(array $fileData)
    {
        if (array_key_exists($fileData['type'], $this->mimeTypes)) {
            $origName = $fileData['tmp_name'];

            $origInfo = getimagesize($origName);

            if (!$origInfo) {
                return true;
            }

            $thumbName = str_replace(
                $this->getFileManager()->getOrigDir(),
                $this->getFileManager()->getThumbDir(),
                $origName
            );

            $this->thumbnailer->resize($origName, $thumbName, $origInfo);
        }

        return true;
    }
}
