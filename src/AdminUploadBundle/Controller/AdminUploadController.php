<?php

namespace AdminUploadBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use AdminUploadBundle\Service\AdminUploadManager;

class AdminUploadController extends Controller
{
    /**
     * @var AdminUploadManager
     */
    protected $uploadManager;

    /**
     * AdminUploadController constructor
     *
     * @param AdminUploadManager $uploadManager
     */
    public function __construct(AdminUploadManager $uploadManager)
    {
        $this->uploadManager = $uploadManager;
    }

    /**
     * @Route("/admin/upload", name="admin_upload")
     */
    public function indexAction(Request $request)
    {
        $CKEditorFuncNum = $request->query->get('CKEditorFuncNum');

        $file = $request->files->get('upload');
        if (!$file) {
            return $this->render('AdminUploadBundle::upload.html.twig', array(
                'CKEditorFuncNum' => $CKEditorFuncNum,
                'errorMessage' => 'Отсутствует файл для закачки!'
            ));
        }

        try {
            $filePath = $this->uploadManager->upload($file);
        } catch (FileException $e) {
            return $this->render('AdminUploadBundle::upload.html.twig', array(
                'CKEditorFuncNum' => $CKEditorFuncNum,
                'errorMessage' => 'Ошибка при загрузке файла!'
            ));
        }

        return $this->render('AdminUploadBundle::upload.html.twig', array(
            'CKEditorFuncNum' => $CKEditorFuncNum,
            'filePath' => $filePath
        ));
    }
}