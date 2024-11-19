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

use League\Flysystem\Filesystem;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use setasign\Fpdi\Tcpdf\Fpdi;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ArquivoController.
 */
class ArquivoController extends Controller
{
    /**
     * Retorna Arquivos.
     *
     * @return JsonResponse
     */
    public function apiGetEditalArquivoAction(Request $request, FrigaEdital $edital)
    {
        $arquivos = \array_map(function(FrigaArquivo $a) {
            $obj = new \stdClass();
            $obj->id = $a->getId();
            $obj->titulo = $a->getTitulo();
            $obj->dataPublicacao = null != $a->getDataPublicacao() ?
                $a->getDataPublicacao()->format('Y-m-d H:i:s') : null;

            return $obj;
        }, \array_reverse($edital->getIdArquivo()->toArray()));

        return new JsonResponse($arquivos);
    }

    /**
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function apiUpdateAction(Request $request, FrigaArquivo $arquivo)
    {
        $em = $this->getDoctrine()->getManager();
        $dt0 = new \DateTime($request->request->get('data'));
        $arquivo->setTitulo($request->request->get('titulo'))
            ->setDataPublicacao($dt0);
        $em->persist($arquivo);
        $em->flush();

        return new JsonResponse($arquivo);
    }

    /**
     * Remove  arquivo.
     *
     * @return JsonResponse
     */
    public function apiDeleteAction(Request $request)
    {
        $obj = new \stdClass();
        $obj->error = false;
        try {
            if (!$request->request->get('id')) {
                throw new \Exception('Este arquivo não existe!');
            }
            $em = $this->getDoctrine()->getManager();
            $arquivo = $em->find(FrigaArquivo::class, $request->request->get('id'));
            if ($arquivo) {
                if ($arquivo->getIdUsuario()->getId() != $this->getUser()->getId() and !$this->isGranted('ROLE_ADMIN')) {
                    $obj->error = true;
                    $obj->msg = 'O arquivo não pode ser removido. você não possui as permissões necessárias para remover este arquivo.';
                    $obj->tipo = 'danger';
                } else {
                    $filesystem = $this->container->get('oneup_flysystem.mount_manager')->getFilesystem('frigadata');
                    if ($filesystem->has($arquivo->getNome())) {
                        $filesystem->delete($arquivo->getNome());
                    }
                    $em->remove($arquivo);
                    $em->flush();
                    $obj->msg = 'Arquivo Excluído com sucesso!';
                    $obj->tipo = 'success';
                }
            }
        } catch (\Exception $e) {
            $obj->error = true;
            $obj->msg = $e->getMessage();
            $obj->tipo = 'danger';
        }

        return new JsonResponse($obj);
    }

    public function visualizarAction(Request $request, FrigaArquivo $arquivo)
    {
        return $this->render('NteAplicacaoFrigaBundle:Arquivo:visualizador.html.twig', [
            'arquivo' => $arquivo,
        ]);
    }

    /**
     * @return BinaryFileResponse
     *
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function downloadAction(Request $request, FrigaArquivo $frigaArquivo = null)
    {
        if (\is_null($frigaArquivo)) {
            return $this->file($this->get('kernel')->getProjectDir() . '/web/assets/img/default_user.png');
        }
        $filesystem = $this->container
            ->get('oneup_flysystem.mount_manager')
            ->getFilesystem('frigadata');

        $file = $filesystem->read($frigaArquivo->getNome());
        echo $file;
    }

    /**
     * @return BinaryFileResponse|Response
     *
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function downloadSiteAction(Request $request, FrigaArquivo $frigaArquivo)
    {
        if (\is_null($frigaArquivo) or 'EDITAL' != $frigaArquivo->getContexto()) {
            if (!$this->isGranted('IS_AUTHENTICATED_FULLY')
                || (\is_object($frigaArquivo)
                    and \is_object($this->getUser())
                    and \property_exists($this->getUser(), 'id')
                    and $frigaArquivo->getIdUsuario()->getId() != $this->getUser()->getId())
            ) {
                return new Response('Arquivo não encontrado');
            }
        }

        $filesystem = $this->container
            ->get('oneup_flysystem.mount_manager')
            ->getFilesystem('frigadata');
        $arquivo = $filesystem->readStream($frigaArquivo->getNome());
        $temp = \tmpfile();
        \fwrite($temp, \stream_get_contents($arquivo));
        $x = \explode('/', $frigaArquivo->getNome());
        \header('Content-Description: File Transfer');
        \header('Content-Type: application/octet-stream');
        \header('Content-Disposition: attachment; filename="' . \end($x) . '"');
        \header('Expires: 0');
        \header('Cache-Control: must-revalidate');
        \header('Pragma: public');
        \header('Content-Length: ' . \filesize(\stream_get_meta_data($temp)['uri']));
        \readfile(\stream_get_meta_data($temp)['uri']);

        return new Response();
    }

    /**
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function apiTesteArquivoAction(Request $request)
    {
        //$filesystemX = $this->container->get('container')->get('oneup_flysystem.usuariofs_filesystem');
        $filesystem = $this->container->get('oneup_flysystem.mount_manager')->getFilesystem('usuario');

        dump($filesystem->getAdapter()->getPathPrefix());
        dump($filesystem->listContents('/tmp/1'));
        dump($filesystem->getMimetype('/tmp/1/5c86a9146c8f3.pdf'));

        //dump($filesystem->read('/tmp/1/5c86a9146c8f3.pdf'));

        $base = $filesystem->getAdapter()->getPathPrefix();
        $p = '/tmp/1/5c86a9146c8f3.pdf';
        $base . $p;
    }

    /**
     * @return BinaryFileResponse|Response|null
     */
    public function assinaturaDigitalAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('arquivo', FileType::class, ['label' => 'Arquivo PDF'])
            ->add('certificado', FileType::class, ['label' => 'Arquivo do Certificado Digital'])
            ->add('senha', PasswordType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $certificado = $form->get('certificado')->getData()->getRealPath();
            $senha = $form->get('senha')->getData();
            $certificado = $this->recuperarCertificado($certificado, $senha);

            if ($certificado->error) {
                $this->addFlash('danger', $certificado->msg);

                return $this->render('@NteAplicacaoFriga/Arquivo/form-assinatural.html.twig', [
                    'form' => $form->createView(),
                ]);
            } else {
                $msg = 'Documento assinado usando o certificado DE: ';
                $msg .= $certificado->info['subject']['CN'];
                $msg .= '/';
                $msg .= $certificado->info['subject']['OU'];
                $this->addFlash('success', $msg);
            }

            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($form->get('arquivo')->getData()->getRealPath());
            for ($pageNo = 1; $pageNo <= $pageCount; ++$pageNo) {
                $pageId = $pdf->ImportPage($pageNo);
                $s = $pdf->getTemplatesize($pageId);
                $pdf->AddPage($s['orientation'], $s);
                $pdf->useImportedPage($pageId);
            }
            $pdf = $this->assinatura($certificado, $pdf);
            $pdf->Output('/tmp/' . $form->get('arquivo')->getData()->getClientOriginalName(), 'I');

            return $this->file('/tmp/' . $form->get('arquivo')->getData()->getClientOriginalName());
        }

        return $this->render('@NteAplicacaoFriga/Arquivo/form-assinatural.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function assinatura($obj, Fpdi $pdf)
    {
        // $raiz = '/var/www/friga/certificado.p7b';
        // openssl pkcs7 -print_certs -in certificado.p7b -out certificado.crt
        $raiz = '/var/www/friga/ac-raiz-v3.cer';
        $info = [
            'Name' => $this->getUser()->getNome(),
            'Location' => 'Santa Maria',
            'Reason' => 'UFSM',
            'ContactInfo' => 'https://ufsm.br/nte',
        ];
        $pdf->setSignature($obj->certificado, $obj->chave, '', $raiz, 2, $info);

        return $pdf;
    }

    public function recuperarCertificado($certificado, $senha)
    {
        $obj = new \stdClass();
        $obj->error = false;
        try {
            $x = \openssl_pkcs12_read(\file_get_contents($certificado), $certs, $senha);
            if ($x) {
                $info = \openssl_x509_parse($certs['cert']);
                $obj->info = $info;
                $obj->certificado = $certs['cert'];
                $obj->chave = $certs['pkey'];

                return $obj;
            }
            $obj->error = true;
            $obj->msg = 'Senha Inválida';
        } catch (\Exception $e) {
            $obj->error = true;
            $obj->msg = $e->getMessage();
        }

        return $obj;
    }

    /**
     * @return BinaryFileResponse|Response|null
     */
    public function moodleProvaAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('arquivo', FileType::class, ['label' => 'Arquivo CSV'])
            ->add('prefixo_usuario', TextType::class)
            ->add('prefixo_curso', TextType::class)
            ->add('categoria', NumberType::class)
            ->add('data', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \SplFileObject $arquivo */
            $arquivo = $form->get('arquivo')->getData()->openFile();
            $prefixo0 = $form->get('prefixo_usuario')->getData();
            $prefixo1 = $form->get('prefixo_curso')->getData();
            $category = $form->get('categoria')->getData();
            $data = $form->get('data')->getData();
            $arquivoCSV0 = '/tmp/course.csv';
            $arquivoCSV1 = '/tmp/users.csv';
            $arquivoZIP = '/tmp/moodleprova.zip';
            if (\is_file($arquivoCSV1)) {
                \unlink($arquivoCSV1);
            }
            if (\is_file($arquivoCSV0)) {
                \unlink($arquivoCSV0);
            }
            if (\is_file($arquivoZIP)) {
                \unlink($arquivoZIP);
            }
            $out0 = \fopen($arquivoCSV0, 'w');
            $out1 = \fopen($arquivoCSV1, 'w');
            \fputcsv($out1, [
                'username',
                'password',
                'email',
                'firstname',
                'lastname',
                'course1',
                'type1',
            ]);

            $curso = [];

            while (!$arquivo->eof()) {
                $dados = $arquivo->fgetcsv();

                if (\count($dados) < 4 or 'CPF' == $dados[3]) {
                    continue;
                }
                $fullname = $dados[4];
                $shortname = (string) $prefixo1 . '_' . \strtolower(\str_replace(' ', '_', $dados[4]));

                $obj = new \stdClass();
                $obj->fullname = $dados[4];
                $obj->shortname = $shortname;
                $obj->category = $category;
                $obj->timestart = $data->format('Y-m-d 08:00:00');
                $obj->timeend = $data->format('Y-m-d 18:00:00');
                $curso[$shortname] = $obj;
                \fputcsv($out1, [
                    (string) $prefixo0 . \str_replace(['.', '-'], '', $dados[3]),
                    (string) \str_replace(['/', '\''], '', $dados[2]),
                    (string) 'naocadastrado@nte.ufsm.br',
                    (string) 'INSCRIÇÃ̀O',
                    (string) \str_pad($dados[0], 8, 0, \STR_PAD_LEFT),
                    $shortname,
                    1,
                ]);
            }

            \uasort($curso, function($a, $b) {
                return \strcmp($b->fullname, $a->fullname);
            });

            \fputcsv($out0, [
                'fullname',
                'shortname',
                'category',
                'timestart',
                'timeend',
            ]);
            foreach ($curso as $c) {
                \fputcsv($out0, [
                    $c->fullname,
                    $c->shortname,
                    $c->category,
                    $c->timestart,
                    $c->timeend,
                ]);
            }

            $zip = new \ZipArchive();
            $zip->open($arquivoZIP, \ZipArchive::CREATE);
            $zip->addFile($arquivoCSV0, '00_course.csv');
            $zip->addFile($arquivoCSV1, '01_users.csv');
            $zip->close();

            return $this->file($arquivoZIP);
        }

        return $this->render('@NteAplicacaoFriga/Arquivo/form-moodle.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
