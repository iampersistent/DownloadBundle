<?php

namespace Bundle\DownloadBundle\Controller;

use Bundle\DownloadBundle\Resources\MediaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DownloadController extends Controller
{

    public function downloadAction($model, $id)
    {
        // look up model (use odm orm based on Entity/Document)
        if (strpos($model, '\\Document\\')) {
            $dm = $this['doctrine.odm.mongodb.document_manager'];
            $object = $dm->find($model, $id);
        } else if (strpos($model, '\\Entity\\')) {
            $em = $this['doctrine.orm.entity_manager'];
            $query = $em->createQuery("select o from $model o where o.id = $id");
            $object = $query->getSingleResult();
        } else {
            throw exception;
        }

        $fullPath = $object->getDownloadFile();
        $fullPath = \str_replace(' ', '%20', $fullPath); // in case it wasn't properly stored

        // if its not a supported protocol, then assume its local and from app root level
        $urlParts = parse_url($fullPath);
        if (!isset($urlParts['scheme'])) {
            $kernelRoot = $this->container->getParameter('kernel.root_dir');
            $appRoot = substr($kernelRoot, 0, strrpos($kernelRoot, '/'));
            $fullPath = $appRoot . '/' . ltrim($fullPath, '/');
        }

        if ($fd = @fopen($fullPath, "r")) {
            if (method_exists($object, 'getFilesize')) {
                $fsize = $object->getFilesize();
            } else {
                $fsize = @filesize($fullPath);
            }
            $path_parts = pathinfo($fullPath);
            $contentType = MediaType::getType(strtolower($path_parts["extension"]));

            header("Content-type: $contentType");
            header("Content-Disposition: attachment; filename=\"" . $path_parts["basename"] . "\""); // use 'attachment' to force a download
            if ($fsize) {
                header("Content-length: $fsize");
            }
            header("Cache-control: private"); //use this to open files directly
            while (!feof($fd)) {
                $buffer = fread($fd, 2048);
                echo $buffer;
            }

            fclose($fd);
            exit;
        }
        return $this->render('DownloadBundle:Download:error:php');
    }

}
