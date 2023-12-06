<?php

/*
 * This file is part of  Friga - https://nte.ufsm.br/friga.
 * (c) Friga
 * Friga is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Friga is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Friga.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Nte\Aplicacao\FrigaBundle\Controller;

use Nte\Aplicacao\FrigaBundle\Model\FrigaArquivo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Class UploadController.
 */
class UploadController extends Controller
{
    /**
     * Página principal.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $arquivos = $em->getRepository(FrigaArquivo::class)->findAll();

        return $this->render('NteAplicacaoCadastrosBundle:frigaarquivo:index.html.twig', [
            'arquivos' => $arquivos,
        ]);
    }

    /**
     * Upload do formulário de inscrição.
     */
    public function uploadAction(Request $request, $tipo)
    {
        $uploadDir = $this->container->getParameter('upload_dir');
        $uploadMIMEAllow = $this->container->getParameter('upload_mime_allow');
        $obj = new \stdClass();
        $obj->error = false;
        $obj->tipo = $tipo;
        $tokenForm = $request->request->get('xtk');
        $tokenHeander = $request->headers->get('X-CSRFToken');

        if (!$this->isCsrfTokenValid('friga-upload-inscricao-cabecalho', $tokenHeander)) {
            $obj->error = true;
            $obj->message = 'Token Inválido (0)';
            $obj->title = 'Erro ao anexar arquivo';

            return new JsonResponse($obj);
        }
        if (!$this->isCsrfTokenValid('friga-upload-inscricao-form', $tokenForm)) {
            $obj->error = true;
            $obj->message = 'Token Inválido (1)';
            $obj->title = 'Erro ao anexar arquivo';

            return new JsonResponse($obj);
        }

        if (null == $request->files->get('file')) {
            $obj->error = true;
            $obj->message = 'NULL FILE';
            $obj->title = 'Erro ao anexar arquivo';

            return new JsonResponse($obj);
        }

        /** @var File $file */
        $file = $request->files->get('file');
        $error = $file->getError();
        if (0 == $error) {
            $md5 = \md5_file($file->getRealPath());
            $extencao = $file->guessExtension();
            $fileName = $tipo . '_' . $md5 . '_.' . $file->guessExtension();
            $mimeType = $file->getMimeType();
            if (false !== \array_search($mimeType, $uploadMIMEAllow)) {
                try {
                    if (!\file_exists($uploadDir)) {
                        \mkdir($uploadDir, 0777);
                    }
                    if (!\file_exists($uploadDir)) {
                        \mkdir($uploadDir . '/tmp/', 0777);
                    }
                    $uploadDir .= '/tmp/';

                    $file->move($uploadDir, $fileName);

                    $registroArquivo = new FrigaArquivo();
                    $registroArquivo
                        ->setHash($md5)
                        ->setPath($uploadDir . $fileName)
                        ->setNome($fileName)
                        ->setExtencao($extencao)
                        ->setTipo($tipo);

                    // Fix -> Erro de registro
                    $this->getDoctrine()->getManager()->persist($registroArquivo);
                    $this->getDoctrine()->getManager()->flush();
                    $obj->fileId = $registroArquivo->getId();
                    $obj->hash = $registroArquivo->getHash();
                } catch (Exception $e) {
                    \unlink($file->getRealPath());
                    $obj->error = true;
                    $obj->message = $e->getMessage();
                    $obj->title = 'Erro ao anexar arquivo';
                }
            } else {
                \unlink($file->getRealPath());
                $obj->error = true;
                $obj->message = 'Tipo de arquivo não suportado';
                $obj->title = 'Erro ao anexar arquivo';
            }
        } elseif ($error = 1) {
            $obj->error = true;
            $obj->message = 'O arquivo enviado excede o limite definido';
            $obj->title = 'Erro ao anexar arquivo';
        } else {
            $obj->error = true;
            $obj->message = 'Houve um erro ao anexar arquivo';
            $obj->title = 'Erro ao anexar arquivo';
        }

        return new JsonResponse($obj);
    }

    /**
     * Download de Arquivo.
     *
     * @return BinaryFileResponse
     */
    public function downloadArquivoAction(Request $request, FrigaArquivo $frigaArquivo)
    {
        $response = new BinaryFileResponse($frigaArquivo->getPath());
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

        return $response;
    }

    /**
     * @param string $hash
     * @param FrigaArquivo $id
     *
     * @return BinaryFileResponse|JsonResponse
     */
    public function downloadArquivoHashAction(Request $request, $hash, $id)
    {
        /** @var FrigaArquivo $frigaArquivo */
        $frigaArquivo = $this->getDoctrine()->getRepository(FrigaArquivo::class)
            ->findOneBy(['hash' => $hash, 'id' => $id]);
        if (!$frigaArquivo) {
            return new JsonResponse([]);
        } elseif (99 == $frigaArquivo->getTipo()) {
            $response = new BinaryFileResponse($frigaArquivo->getPath());
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

            return $response;
        } else {
            return new JsonResponse([]);
        }
    }

    public function removeFileAction(Request $request, $id, $hash)
    {
        $obj = new \stdClass();
        $obj->error = false;
        $obj->qtdRemovido = false;

        $frigaArquivo = $this->getDoctrine()->getManager()->getRepository(FrigaArquivo::class);
        $arquivo = $frigaArquivo->findOneBy(['id' => $id, 'hash' => $hash]);
        if ($arquivo) {
            try {
                if (1 == \count($frigaArquivo->findByHash($hash)) and \file_exists($arquivo->getPath())) {
                    \unlink($arquivo->getPath());
                }
                $obj->titpo = $arquivo->getTipo();
                $this->getDoctrine()->getManager()->remove($arquivo);
                $this->getDoctrine()->getManager()->flush();
                $obj->qtdRemovido = true;
                $obj->message = 'Arquivo removido com sucesso!';
            } catch (Exception $e) {
                $obj->error = true;
                $obj->message = $e->getMessage();
                $obj->title = 'Erro ao remover arquivo';
            }
        } else {
            $obj->title = '';
            $obj->message = 'Nenhum arquivo removido!';
        }

        return new JsonResponse($obj);
    }

    /**
     * Displays a form to edit an existing frigaArquivo entity.
     */
    public function renomearAction(Request $request)
    {
        $frigaArquivo = $this->getDoctrine()->getManager()->find(FrigaArquivo::class, $request->request->get('id'));
        $frigaArquivo->setTitulo($request->request->get('titulo'));
        $this->getDoctrine()->getManager()->persist($frigaArquivo);
        $this->getDoctrine()->getManager()->flush();

        $obj = new \stdClass();
        $obj->id = $frigaArquivo->getId();
        $obj->titulo = $frigaArquivo->getTitulo();
        $obj->hash = $frigaArquivo->getHash();
        $obj->url = $this->generateUrl('frigaarquivo_download', ['id' => $frigaArquivo->getId()]);

        return new JsonResponse($obj);
    }
}
