<?php

namespace Nte\Aplicacao\FrigaBundle\Controller;

use League\Flysystem\Filesystem;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\UsuarioBundle\Entity\Usuario;
use phpseclib\Crypt\Hash;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpClient\Response\ResponseStream;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Stream;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfReader\PdfReaderException;
use setasign\Fpdi\Tcpdf\Fpdi;

/**
 * Class ArquivoController
 * @package Nte\Aplicacao\FrigaBundle\Controller
 */
class ArquivoController extends Controller
{
    /**
     * Retorna Arquivos
     *
     * @param Request $request
     * @param FrigaEdital $edital
     *
     * @return JsonResponse
     */
    public function apiGetEditalArquivoAction(Request $request, FrigaEdital $edital)
    {
        $arquivos = array_map(function (FrigaArquivo $a) {
            $obj = new \stdClass();
            $obj->id = $a->getId();
            $obj->titulo = $a->getTitulo();
            $obj->dataPublicacao = $a->getDataPublicacao() != null ?
                $a->getDataPublicacao()->format('Y-m-d H:i:s') : null;

            return $obj;
        }, array_reverse($edital->getIdArquivo()->toArray()));

        return new JsonResponse($arquivos);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function apiUpdateAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $arquivo = $em->find(FrigaArquivo::class, $request->request->get('id'));

        if (!$arquivo) {
            return new JsonResponse();
        }
        $arquivo->setTitulo($request->request->get('titulo'))
            ->setDataPublicacao(new \DateTime($request->request->get('dataPublicacao')));
        $em->persist($arquivo);
        $em->flush();


        return new JsonResponse($arquivo);
    }

    /**
     * Remove  arquivo
     *
     * @param Request $request
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
            $em = $this->getDoctrine()->getManager();;
            $arquivo = $em->find(FrigaArquivo::class, $request->request->get('id'));
            if ($arquivo) {
                if ($arquivo->getIdUsuario()->getId() != $this->getUser()->getId() and !$this->isGranted('ROLE_ADMIN')) {
                    $obj->error = true;
                    $obj->msg = "O arquivo não pode ser removido. você não possui as permissões necessárias para remover este arquivo.";
                    $obj->tipo = "danger";
                } else {
                    $filesystem = $this->container->get('oneup_flysystem.mount_manager')->getFilesystem('frigadata');
                    if ($filesystem->has($arquivo->getNome())) {
                        $filesystem->delete($arquivo->getNome());
                    }
                    $em->remove($arquivo);
                    $em->flush();
                    $obj->msg = "Arquivo Excluído com sucesso!";
                    $obj->tipo = "success";
                }
            }
        } catch (\Exception $e) {
            $obj->error = true;
            $obj->msg = $e->getMessage();
            $obj->tipo = "danger";
        }
        return new JsonResponse($obj);
    }

    public function visualizarAction(Request $request, FrigaArquivo $arquivo)
    {

        return $this->render('NteAplicacaoFrigaBundle:Arquivo:visualizador.html.twig', [
            'arquivo' => $arquivo
        ]);
    }

    /**
     * @param Request $request
     * @param FrigaArquivo $frigaArquivo
     * @return BinaryFileResponse
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function downloadAction(Request $request, FrigaArquivo $frigaArquivo = null)
    {

        if (is_null($frigaArquivo)) {
            return $this->file($this->get('kernel')->getProjectDir() . '/web/assets/img/default_user.png');
        }
        $filesystem = $this->container
            ->get('oneup_flysystem.mount_manager')
            ->getFilesystem('frigadata');

        $file = $filesystem->read($frigaArquivo->getNome());
        echo $file;

    }

    /**
     * @param Request $request
     * @param FrigaArquivo $frigaArquivo
     * @return BinaryFileResponse|Response
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function downloadSiteAction(Request $request, FrigaArquivo $frigaArquivo)
    {
        if (is_null($frigaArquivo) or $frigaArquivo->getContexto() != "EDITAL") {
            return $this->file($this->get('kernel')->getProjectDir() . '/web/assets/img/default_user.png');
        }
        $filesystem = $this->container
            ->get('oneup_flysystem.mount_manager')
            ->getFilesystem('frigadata');
        $arquivo = $filesystem->readStream($frigaArquivo->getNome());
        $temp = tmpfile();
        fwrite($temp, stream_get_contents($arquivo));
        $x = explode('/', $frigaArquivo->getNome());
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . end($x) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize(stream_get_meta_data($temp)['uri']));
        readfile(stream_get_meta_data($temp)['uri']);
        return new Response();

    }

    /**
     * @param Request $request
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function apiTesteArquivoAction(Request $request)
    {

        //$filesystemX = $this->container->get('container')->get('oneup_flysystem.usuariofs_filesystem');
        $filesystem = $this->container->get('oneup_flysystem.mount_manager')->getFilesystem('usuario');

        dump($filesystem->getAdapter()->getPathPrefix());
        dump($filesystem->listContents("/tmp/1"));
        dump($filesystem->getMimetype('/tmp/1/5c86a9146c8f3.pdf'));

        //dump($filesystem->read('/tmp/1/5c86a9146c8f3.pdf'));

        $base = $filesystem->getAdapter()->getPathPrefix();
        $p = '/tmp/1/5c86a9146c8f3.pdf';
        $base . $p;
    }

    /**
     * Teste PDF
     *
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function pdfTesteAction(Request $request)
    {
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('SCDP NTE');
        $pdf->SetTitle('TEste de Geração de documento autoassinado');
        $pdf->SetSubject('Teste de geração de documento autoassinado');
        $pdf->SetKeywords('PDF assinado, openssl, opensource, gpl');
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        // $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // $pdf->setLanguageArray($l);
        $certificate = 'file:///var/www/friga/certificado.crt';
        $info = array(
            'Name' => 'SCDP',
            'Location' => 'Sala 124',
            'Reason' => 'SCDP/NTE/UFSM',
            'ContactInfo' => 'https://scdp.nte.ufsm.br',
        );
        $pdf->setSignature($certificate, $certificate, '', '', 2, $info);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->AddPage();
        $text = 'Este é um exemplo de <b color="#FF0000">Teste </b>';
        $pdf->writeHTML($text, true, 0, true, 0);
        // $pdf->Image('../images/tcpdf_signature.png', 180, 60, 15, 15, 'PNG');
        $pdf->setSignatureAppearance(180, 60, 15, 15);
        $pdf->addEmptySignatureAppearance(180, 80, 15, 15);
        $pdf->Output('/tmp/test.pdf', 'I');
        return $this->file('/tmp/test.pdf');
    }

    /**
     * @param Request $request
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
                $msg = "Documento assinado usando o certificado DE: ";
                $msg .= $certificado->info['subject']['CN'];
                $msg .= "/";
                $msg .= $certificado->info['subject']['OU'];
                $this->addFlash('success', $msg);
            }

            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($form->get('arquivo')->getData()->getRealPath());
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
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
        $info = array(
            'Name' => $this->getUser()->getNome(),
            'Location' => 'Santa Maria',
            'Reason' => 'UFSM',
            'ContactInfo' => 'https://ufsm.br/nte',
        );
        $pdf->setSignature($obj->certificado, $obj->chave, '', $raiz, 2, $info);
        return $pdf;
    }

    public function recuperarCertificado($certificado, $senha)
    {
        $obj = new \stdClass();
        $obj->error = false;
        try {
            $x = openssl_pkcs12_read(file_get_contents($certificado), $certs, $senha);
            if ($x) {
                $info = openssl_x509_parse($certs['cert']);
                $obj->info = $info;
                $obj->certificado = $certs['cert'];
                $obj->chave = $certs['pkey'];
                return $obj;
            }
            $obj->error = true;
            $obj->msg = "Senha Inválida";
        } catch (\Exception $e) {
            $obj->error = true;
            $obj->msg = $e->getMessage();
        }
        return $obj;
    }

    /**
     * @param Request $request
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
            $arquivoCSV0 = "/tmp/course.csv";
            $arquivoCSV1 = "/tmp/users.csv";
            $arquivoZIP = "/tmp/moodleprova.zip";
            if (is_file($arquivoCSV1)) {
                unlink($arquivoCSV1);
            }
            if (is_file($arquivoCSV0)) {
                unlink($arquivoCSV0);
            }
            if (is_file($arquivoZIP)) {
                unlink($arquivoZIP);
            }
            $out0 = fopen($arquivoCSV0, 'w');
            $out1 = fopen($arquivoCSV1, 'w');
            fputcsv($out1, [
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
                if ($dados[3] == "CPF") {
                    continue;
                }
                $fullname = $dados[4];
                $shortname = (string)$prefixo1 . "_" . strtolower(str_replace(' ', '_', $dados[4]));

                $obj = new \stdClass();
                $obj->fullname = $dados[4];
                $obj->shortname = $shortname;
                $obj->category = $category;
                $obj->timestart = $data->format('Y-m-d 08:00:00');
                $obj->timeend = $data->format('Y-m-d 18:00:00');
                $curso[$shortname] = $obj;
                fputcsv($out1, [
                    (string)$prefixo0 . str_replace(['.', '-'], '', $dados[3]),
                    (string)str_replace(['/', '\''], '', $dados[2]),
                    (string)"naocadastrado@nte.ufsm.br",
                    (string)"INSCRIÇÃ̀O",
                    (string)str_pad($dados[0], 8, 0,STR_PAD_LEFT),
                    $shortname,
                    1,
                ]);
            }

            uasort($curso, function ($a, $b){
                return strcmp($b->fullname, $a->fullname);
            });

            fputcsv($out0, [
                'fullname',
                'shortname',
                'category',
                'timestart',
                'timeend',
            ]);
            foreach ($curso as $c) {
                fputcsv($out0, [
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