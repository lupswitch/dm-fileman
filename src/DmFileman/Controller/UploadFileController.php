<?php

namespace DmFileman\Controller;

use DmCommon\Controller\TestableController;
use Zend\View\Model\ViewModel;
use DmFileman\Service\FileManager\FileManager;
use DmFileman\Form\UploadFileForm;
use DmFileman\View\Helper\UserText;
use DmFileman\Service\Thumbnailer\Thumbnailer;

/**
 * Class UploadFileController
 *
 * @package DmFileman\Controller
 *
 * @method ViewModel layout(string $template = null)
 */
class UploadFileController extends TestableController
{
    use CurrentPathTrait;

    /** @var FileManager */
    private $fileManager;

    /** @var UploadFileForm */
    private $uploadFileForm;

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
     * @param UploadFileForm        $uploadFileForm
     * @param Thumbnailer           $thumbnailer
     * @param UserText              $userText
     */
    public function __construct(
        FileManager $fileManager,
        UploadFileForm $uploadFileForm,
        Thumbnailer $thumbnailer,
        UserText $userText
    ) {
        $this->fileManager = $fileManager;

        $this->uploadFileForm = $uploadFileForm;

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
    private function getOrigDir()
    {
        return $this->getFileManager()->getOrigDir($this->getCurrentPath());
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
            $fileManager = $this->getFileManager();

            return $this->thumbnailer
                ->resizeOrigImage($fileData['tmp_name'], $fileManager->getOrigDir(), $fileManager->getThumbDir());
        }

        return true;
    }
}