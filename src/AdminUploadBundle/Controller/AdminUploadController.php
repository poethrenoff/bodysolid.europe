<?php

namespace AdminUploadBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use AdminUploadBundle\Util\Util;

class AdminUploadController extends Controller
{
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
            $fileName = Util::getFileName($file->getClientOriginalName());
            $file->move($this->getParameter('admin_upload')['directory'], $fileName);
            $filePath = Util::getFilePath($fileName, $this->getParameter('admin_upload')['alias']);
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