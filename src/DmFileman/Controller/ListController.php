<?php

namespace DmFileman\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use DmFileman\Service\FileManager\FileManager;
use DmFileman\Form\CreateDirectoryForm;
use DmFileman\Form\DeleteFileForm;
use DmFileman\Form\UploadFileForm;

/**
 * Class ListController
 *
 * @package DmFileman\Controller
 *
 * @method ViewModel layout(string $template = null)
 */
class ListController extends AbstractActionController
{
    use CurrentPathTrait;

    /** @var FileManager */
    private $fileManager;

    /** @var CreateDirectoryForm */
    private $createDirForm;

    /** @var UploadFileForm */
    private $uploadFileForm;

    /** @var DeleteFileForm */
    private $deleteFileForm;

    /** @var bool */
    private $initialized = false;

    /**
     * @param FileManager         $fileManager
     * @param CreateDirectoryForm $createDirectoryForm
     * @param UploadFileForm      $uploadFileForm
     * @param DeleteFileForm      $deleteFileForm
     */
    public function __construct(
        FileManager $fileManager,
        CreateDirectoryForm $createDirectoryForm,
        UploadFileForm $uploadFileForm,
        DeleteFileForm $deleteFileForm
    ) {
        $this->fileManager = $fileManager;

        $this->createDirForm = $createDirectoryForm;

        $this->uploadFileForm = $uploadFileForm;

        $this->deleteFileForm = $deleteFileForm;
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
    public function indexAction()
    {
        return $this->redirect()->toRoute('filemanager/list', ['dir' => '/']);
    }

    /**
     * @return ViewModel
     */
    public function listAction()
    {
        $this->createDirForm->build();

        $this->uploadFileForm->build();

        $this->deleteFileForm->build();

        $viewData = [
            'list'       => $this->getFileManager()->getList(),
            'currentDir' => $this->getCurrentPath(),
            'createForm' => $this->createDirForm,
            'uploadForm' => $this->uploadFileForm,
            'deleteForm' => $this->deleteFileForm,
        ];

        $this->layout('layout/filemanager.phtml');

        return new ViewModel($viewData);
    }

    /**
     * @return \Zend\Http\Response
     */
    public function refreshAction()
    {
        return $this->redirect()->toRoute('filemanager/list', ['dir' => $this->getCurrentPath()]);
    }
}
