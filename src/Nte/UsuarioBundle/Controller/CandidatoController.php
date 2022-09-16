<?php

namespace Nte\UsuarioBundle\Controller;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoPontuacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoProjetoParticipante;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoRecurso;
use Nte\Aplicacao\FrigaBundle\Form\FrigaRecursoCandidatoType;
use Nte\UsuarioBundle\Entity\Usuario;
use Nte\UsuarioBundle\Form\InscricaoProjetoType;
use Nte\UsuarioBundle\Form\InscricaoType;
use phpseclib\Crypt\Hash;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class CandidatoController extends Controller
{
    /**
     * Index dos usuários
     * @return Response
     */
    public function indexAction(Request $request)
    {

        return $this->render('@NteUsuario/Candidato/index.html.twig', [

        ]);
    }

    /**
     * @param Request $request
     * @param FrigaInscricao $inscricao
     * @return RedirectResponse
     * @throws Exception
     */
    public function cancelarInscricaoAction(Request $request, FrigaInscricao $inscricao)
    {
        if ($inscricao->getIdUsuario()->getId() == $this->getUser()->getId()) {
            if ($inscricao->getIdEdital()->getPeriodoInscricaoHabilitado()) {
                if ($inscricao->getIdSituacao() == 0) {
                    $inscricao->setIdSituacao(-999);
                    $this->getDoctrine()->getManager()->flush();
                    $this->addFlash('success', "Inscrição cancelada com sucesso");
                } elseif ($inscricao->getIdSituacao() - 999) {
                    $this->addFlash('success', "Inscrição anteriormente cancelada.");
                } else {
                    $this->addFlash('error', "Esta inscrição não pode ser cancelada.");
                }
            } else {
                $this->addFlash('error', "Esta inscrição não pode ser cancelada. Fora do período de inscrição");
            }
        } else {
            if ($this->isGranted('ROLE_ADMIN')) {
                $this->addFlash('success', "xxxx");
            } else {
                $this->addFlash('error', "Esta inscrição não pode ser cancelada. Você não tem permissão para cancelar esta inscrição");
            }
        }
        return $this->redirectToRoute('nte_usuario_candidato_inscricao_index');
    }

    /**
     * Inscrição concluída
     *
     * @param Request $request
     * @param FrigaInscricao $inscricao
     * @return Response
     */
    public function inscricaoConcluidaAction(Request $request, FrigaInscricao $inscricao)
    {
        return $this->render('@NteUsuario/Candidato/status.html.twig', [
            'status' => 0,
            'inscricao' => $inscricao,
        ]);
    }

    /**
     * Inscrição concluída
     *
     * @param Request $request
     * @param FrigaInscricao $inscricao
     * @return Response
     */
    public function inscricaoRealizadaAction(Request $request, FrigaInscricao $inscricao)
    {
        return $this->render('@NteUsuario/Candidato/ver-inscricao.html.twig', [
            'inscricao' => $inscricao,

        ]);
    }

    /**
     * Index inscrições do candidato
     *
     * @param Request $request
     * @return Response
     */
    public function indexInscricoesAction(Request $request)
    {
        return $this->render('@NteUsuario/Candidato/index-inscricao.html.twig', [
        ]);
    }

    /**
     * Index Recursos do candidato
     *
     * @param Request $request
     * @return Response
     */
    public function indexRecursosAction(Request $request)
    {
        return $this->render('@NteUsuario/Candidato/index-recurso.html.twig', [
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function formRecursoAction(Request $request, FrigaEditalEtapa $etapa, FrigaInscricao $inscricao)
    {
        $em = $this->getDoctrine()->getManager();

        $recurso = new FrigaInscricaoRecurso();
        $recurso->setIdEditalEtapa($etapa->getIdEtapa())->setIdInscricao($inscricao)->setIdSituacao(0);
        $form = $this->createForm(FrigaRecursoCandidatoType::class, $recurso);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $inscricao->setIdSituacaoAnterior($inscricao->getIdSituacao());
            $inscricao->setIdSituacao(5);
            $em->persist($recurso);
            $em->flush();

            $arquivos = $request->request->get('arquivos');
            if (isset($arquivos) and is_array($arquivos)) {
                foreach ($arquivos as $arquivo) {
                    $a = $em->find(FrigaArquivo::class, $arquivo);
                    if (!$a) {
                        $message = \Swift_Message::newInstance()
                            ->setSubject('Arquivo não encontrado. - ' . $inscricao->getUuid())
                            ->setBcc(['luizguilherme@nte.ufsm.br'])
                            ->setFrom('processoseletivo@nte.ufsm.br', "Processo Seletivo")
                            ->setBody("Arquivo ID: {$arquivo} não encontrado");

                        $this->get('mailer')
                            ->send($message);
                        continue;
                    }
                    $recurso->addIdArquivo($a);
                    $a->addIdInscricaoRecurso($recurso);
                    $em->persist($a);
                    $em->persist($inscricao);
                    $em->flush();
                    //dump($a);
                }
            }
            return $this->redirectToRoute('nte_usuario_candidato_recursos_index');
        }

        return $this->render('@NteUsuario/Candidato/form-recurso.html.twig', [
            'etapa' => $etapa,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function verRecursoAction(Request $request, FrigaInscricaoRecurso $recurso)
    {
        return $this->render('@NteUsuario/Candidato/ver-recurso.html.twig', [
            'recurso' => $recurso
        ]);
    }

    /**
     * Index Convocações do candidato
     *
     * @param Request $request
     * @return Response
     */
    public function indexConvocacaoAction(Request $request)
    {
        return $this->render('@NteUsuario/Candidato/index-convocacao.html.twig', [
        ]);
    }

    /**
     * Index editais abertos
     *
     * @param Request $request
     * @return Response
     */
    public function indexEditaisAction(Request $request)
    {
        return $this->render('@NteUsuario/Candidato/index-edital.html.twig', [

        ]);
    }

    /**
     * Index editais abertos
     *
     * @param Request $request
     * @return Response
     */
    public function indexPerfilAction(Request $request)
    {
        return $this->render('@NteUsuario/Candidato/index-perfil.html.twig', [

        ]);
    }

    /**
     * Index editais abertos
     *
     * @param Request $request
     * @return Response
     */
    public function indexResultadoAction(Request $request)
    {
        return $this->render('@NteUsuario/Candidato/index-resultado.html.twig', [

        ]);
    }
    /**
     * @param Request $request
     * @param FrigaInscricaoProjetoParticipante $inscricao
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function inscricaoProjetoAction(Request $request, FrigaInscricaoProjetoParticipante  $inscricao)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(InscricaoProjetoType::class, $inscricao);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $inscricao->setIdUsuario($this->getUser());
            $em->persist($inscricao);
            $em->flush();
            /*
            $inscricao->setIdSituacaoAnterior($inscricao->getIdSituacao());
            $inscricao->setIdSituacao(5);
            $em->persist($recurso);
            $em->flush();
*/
            $arquivos = $request->request->get('arquivos');
            if (isset($arquivos) and is_array($arquivos)) {
                foreach ($arquivos as $arquivo) {
                    $a = $em->find(FrigaArquivo::class, $arquivo);
                    if (!$a) {
                        $message = \Swift_Message::newInstance()
                            ->setSubject('Arquivo não encontrado. - ' . $inscricao->getUuid())
                            ->setBcc(['luizguilherme@nte.ufsm.br'])
                            ->setFrom('processoseletivo@nte.ufsm.br', "Processo Seletivo")
                            ->setBody("Arquivo ID: {$arquivo} não encontrado");

                        $this->get('mailer')
                            ->send($message);
                        continue;
                    }
                    $inscricao->addIdArquivo($a);
                    $a->addIdProjetoParticipante($inscricao);
                    $em->persist($a);
                    $em->persist($inscricao);
                    $em->flush();
                }
            }

            return $this->redirectToRoute('nte_usuario_candidato_homepage');
        }

        return $this->render('@NteUsuario/Candidato/form-inscricao-projeto.html.twig', [
            'inscricao' => $inscricao,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param FrigaEdital $edital
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function inscricaoAction(Request $request, FrigaEdital $edital)
    {
        if (!($edital->getPublico() == 1 && $edital->getPeriodoInscricao()->getAndamentoPrazo() > 0)
            or !($edital->getPublico() == 2 && $edital->getPeriodoInscricao()->getAndamentoPrazo() < 100)
            or !($edital->getPublico() == 3)
            or !($this->isGranted('ROLE_ADMIN'))
        ) {
            // return $this->redirectToRoute('nte_site_homepage');
        }
        if (!($this->isGranted('ROLE_ADMIN'))) {
            if (!$edital->getPeriodoInscricaoHabilitado()) {
                return $this->redirectToRoute('nte_site_edital', [
                    'id' => $edital->getId(), 'url' => $edital->getUrl()
                ]);
            }
        }

        /** @var Usuario $usuario */
        $usuario = $this->getUser();
        $inscricao = new FrigaInscricao();
        $inscricao->setNome($usuario->getNome())
            ->setIdUsuario($usuario)
            ->setDataNascimento($usuario->getDataNascimento())
            ->setCpf($usuario->getCpf())
            ->setRgNro($usuario->getRgNro())
            ->setRgOrgaoExpedidor($usuario->getRgOrgaoExpedidor())
            ->setContatoTelefone1($usuario->getContatoTelefone1())
            ->setContatoTelefone2($usuario->getContatoTelefone2())
            ->setContatoEmail($usuario->getEmail())
            ->setEnderecoCep($usuario->getEnderecoCep())
            ->setEnderecoLogradouro($usuario->getEnderecoLogradouro())
            ->setEnderecoNumero($usuario->getEnderecoNumero())
            ->setEnderecoComplemento($usuario->getEnderecoComplemento())
            ->setEnderecoBairro($usuario->getEnderecoBairro())
            ->setEnderecoMunicipio($usuario->getEnderecoMunicipio())
            ->setEnderecoUf($usuario->getEnderecoUf())
            ->setIdEdital($edital)
            ->setIdSituacao(0);


        $form = $this->createForm(InscricaoType::class, $inscricao, ['entityManager' => $this->getDoctrine()->getManager(),]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            if ($edital->getModeloInscricao() == 1) {
                if(is_array($inscricao->getProjetoAreaConhecimento())){
                    $inscricao->setProjetoAreaConhecimento(implode(',', $inscricao->getProjetoAreaConhecimento()));
                }
            }

            try {

                $arquivos = $request->request->get('arquivos');

                /** @var QueryBuilder $inscricaoAnterior */
                $inscricaoAnterior = $em->createQueryBuilder()
                    ->update(FrigaInscricao::class, 'i')
                    ->set('i.idSituacao', -999)
                    ->where("i.idEdital = :edital and i.idUsuario  = :usuario ")
                    ->setParameter('edital', $edital)
                    ->setParameter('usuario', $this->getUser());

                //Verifica o tipo de inscrição e se for necessário, cancela as inscrições anteriores
                switch ($edital->getTipoInscricao()) {
                    case 0: //Inscrição única
                        $inscricaoAnterior->getQuery()->execute();
                        break;

                    case 1: //Inscrição múltipla - cargo único
                        $inscricaoAnterior->andWhere('i.idCargo = :cargo')
                            ->setParameter('cargo', $inscricao->getIdCargo())
                            ->getQuery()
                            ->execute();
                        break;

                    case 2: //Inscrição múltipla  - cota única
                        $inscricaoAnterior->andWhere('i.idCota = :cota')
                            ->setParameter('cota', $inscricao->getIdCota())
                            ->getQuery()
                            ->execute();
                        break;

                    case 3: //Inscrição múltipla
                        //não faz nada;
                        break;

                    case 4: //Inscrição múltipla/Cargo limitado
                        $inscricaoAnterior = new ArrayCollection($em->createQueryBuilder()
                            ->select('i')
                            ->from(FrigaInscricao::class, 'i')
                            ->where("i.idEdital = :edital and i.idUsuario  = :usuario ")
                            ->andWhere('i.idCargo = :cargo')
                            ->setParameter('cargo', $inscricao->getIdCargo())
                            ->setParameter('edital', $edital)
                            ->setParameter('usuario', $this->getUser())
                            ->orderBy('i.id','ASC')
                            ->getQuery()
                            ->getResult())
                        ;
                        if($inscricaoAnterior->count() > ($edital->getTipoInscricaoLimite() -1)){

                        }
                        break;

                    case 5: //Inscrição múltipla/Lista limitado
                        //não faz nada;
                        break;
                }
                $em->persist($inscricao);
                $em->flush();

                $pontuacaoSalva = [];
                $pontuacaoChave = [];
                foreach ($request->request->get('nte_inscricao') as $chave => $valor) {
                    $idCategoria = false;
                    $pt = false;

                    //Filtra os itens de pontuação enviados pelo formulário
                    if (strpos($chave, "pt__") === 0 or strpos($chave, "cat__") === 0) {
                        $pontuacao = new FrigaInscricaoPontuacao();
                        $pontuacao->setIdInscricao($inscricao)
                            ->setIdEditalEtapa($edital->getPeriodoInscricao());

                        // Captura o valor da pontuação
                        if (strpos($chave, "pt__") === 0) {
                            $idPontuacao = intval(str_replace("pt__", "", $chave));
                            $pt = $em->find(FrigaEditalPontuacao::class, $idPontuacao);
                            $pontuacao
                                ->setIdEditalPontuacao($pt)
                                ->setValorInformado(floatval($valor));
                        }
                        // Captura o valor da pontuação através da categoria
                        if (strpos($chave, "cat__") === 0) {
                            $idCategoria = intval(str_replace("cat__", "", $chave));
                            $idPontuacao = intval(str_replace("pt__", "", intval($valor)));
                            $pt = $em->find(FrigaEditalPontuacao::class, $idPontuacao);
                            if ($pt) {
                                $pontuacao->setIdEditalPontuacao($pt)
                                    ->setValorInformado($pt->getValorMaximo());
                            }
                        }
                        if ($pt) {
                            $em->persist($pontuacao);
                            $em->flush();
                            $pontuacaoSalva[$pt->getId()] = $pontuacao;
                            $pontuacaoChave[$chave] = $pontuacao;
                            if ($idCategoria) {
                                $pontuacaoSalva[$idCategoria] = $pontuacao;
                            }
                        }
                    }
                }
                foreach ($request->request->get('nte_inscricao') as $chave => $valor) {
                    if (strpos($chave, "tpt__") === 0 or strpos($chave, "tcat__") === 0) {
                        if (key_exists(ltrim($chave, $chave[0]), $pontuacaoChave)) {
                            $pontuacaoChave[ltrim($chave, $chave[0])]->setValorTexto($valor);
                            $em->persist($pontuacaoChave[ltrim($chave, $chave[0])]);
                            $em->flush();
                        }
                    }
                }
                $projetoParticipante = [];
                if ($edital->getModeloInscricao() == 1) {
                    $projetoParticipante[0]['nome'] = $inscricao->getNome();
                    $projetoParticipante[0]['email'] = $inscricao->getContatoEmail();
                    $projetoParticipante[0]['usuario']= $this->getUser();
                    foreach ($request->request->get('nte_inscricao') as $chave => $valor) {
                        if (strpos($chave, "projetoParticipanteNome") === 0 and $valor !="" ) {
                            $pn = intval(str_replace("projetoParticipanteNome", "", $chave));
                            $projetoParticipante[$pn]['nome'] = $valor;
                        }
                        if (strpos($chave, "projetoParticipanteEmail") === 0 and $valor !="") {
                            $pn = intval(str_replace("projetoParticipanteEmail", "", $chave));
                            $projetoParticipante[$pn]['email'] = $valor;
                            $projetoParticipante[$pn]['usuario'] = $em->getRepository(Usuario::class)->findOneByEmail($valor);
                        }
                    }
                    $ix= 0;
                    foreach ($projetoParticipante as $p){
                        $pp = new FrigaInscricaoProjetoParticipante();
                        $pp->setNome($p['nome'])
                            ->setEmail($p['email'])
                            ->setIdUsuario($p['usuario'])
                            ->setIdInscricao($inscricao);
                        ;
                        if($ix==0){
                            $pp->setConfirmacao(1);
                        }
                        $em->persist($pp);
                        $em->flush();
                        if($ix>0){
                            $this->enviarEmailPP($pp);
                        }
                        $ix++;
                    }
                }

                if (isset($arquivos) and is_array($arquivos)) {
                    foreach ($arquivos as $arquivo) {
                        $a = $em->find(FrigaArquivo::class, $arquivo);
                        if (!$a) {
                            $message = \Swift_Message::newInstance()
                                ->setSubject('Arquivo não encontrado. - ' . $inscricao->getUuid())
                                ->setBcc(['luizguilherme@nte.ufsm.br'])
                                ->setFrom('processoseletivo@nte.ufsm.br', "Processo Seletivo")
                                ->setBody("Arquivo ID: {$arquivo} não encontrado");

                            $this->get('mailer')
                                ->send($message);
                            continue;
                        }
                        if (array_key_exists($a->getIdContexto(), $pontuacaoSalva)) {
                            /** @var FrigaInscricaoPontuacao $pt */
                            $pt = $pontuacaoSalva[$a->getIdContexto()];
                            $a->addIdPontuacao($pt);
                            $pt->addIdArquivo($a);
                        } else {
                            $inscricao->addIdArquivo($a);
                            $a->addIdInscricao($inscricao);
                        }
                        $em->persist($a);
                        $em->persist($inscricao);
                        $em->flush();
                    }
                }
            } catch (Exception $e) {
                $message = \Swift_Message::newInstance()
                    ->setSubject('ERRO com a Inscrição - ' . $inscricao->getUuid())
                    ->setBcc(['alexandre@nte.ufsm.br', 'luizguilherme@nte.ufsm.br'])
                    ->setFrom('processoseletivo@nte.ufsm.br', "Processo Seletivo")
                    ->setBody($e->getMessage());
                $this->get('mailer')->send($message);

            }
            $em->clear();
            $inscricao = $em->find(FrigaInscricao::class, $inscricao->getId());
            $this->enviarEmail($inscricao);
            return $this->redirectToRoute('nte_usuario_candidato_inscricao_concluida', ['uuid' => $inscricao->getUuid()]);
        }
        return $this->render('@NteUsuario/Candidato/form-inscricao.html.twig', [
            'form' => $form->createView(),
            'edital' => $edital,
        ]);
    }

    /**
     * @param Request $request
     * @param FrigaInscricao $inscricao
     * @return Response
     */
    public function testeAction(Request $request, FrigaInscricao $inscricao)
    {
        $this->enviarEmail($inscricao);
        return $this->render('@NteUsuario/Candidato/email.html.twig', [
            'inscricao' => $inscricao
        ]);
    }


    /**
     * @param FrigaInscricao $inscricao
     */
    private function enviarEmail(FrigaInscricao $inscricao)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Inscrição - ' . $inscricao->getUuid())
            ->setFrom('processoseletivo@nte.ufsm.br', "Processo Seletivo")
            ->setTo($inscricao->getContatoEmail())
            ->setBcc(['alexandre@nte.ufsm.br', 'luizguilherme@nte.ufsm.br'])
            ->setBody($this->renderView('@NteUsuario/Candidato/email.html.twig', [
                'inscricao' => $inscricao
            ]), 'text/html');
        $this->get('mailer')->send($message);
    }

    /**
     * @param FrigaInscricaoProjetoParticipante $inscricao
     */
    private function enviarEmailPP(FrigaInscricaoProjetoParticipante $inscricao)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Inscrição em projeto - ' . $inscricao->getUuid())
            ->setFrom('processoseletivo@nte.ufsm.br', "Processo Seletivo")
            ->setTo($inscricao->getEmail())
         //   ->setBcc(['alexandre@nte.ufsm.br', 'luizguilherme@nte.ufsm.br'])
            ->setBody($this->renderView('@NteUsuario/Candidato/email.pp.html.twig', [
                'inscricao' => $inscricao
            ]), 'text/html');
        $this->get('mailer')->send($message);
        //dump($message);

    }

    /**
     * @param Request $request
     * @param null $uuid
     * @param null $arquivo
     * @return Response
     */
    public function downloadArquivoAction(Request $request, $uuid = null, $arquivo = null){
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var FrigaInscricaoProjetoParticipante|null $inscricacao */
        $inscricacao = $em->getRepository(FrigaInscricaoProjetoParticipante::class)->findOneByUuid($uuid);

        if(is_null($inscricacao) or is_null($arquivo)){
            return new Response();
        }
        $frigaArquivo = $inscricacao->getIdInscricao()->getIdArquivo()->filter(function (FrigaArquivo  $a) use ($arquivo){
            return $a->getId() == $arquivo;
        })->first();
        if(is_null($frigaArquivo)){
            return new Response();
        }
        $filesystem = $this->container
            ->get('oneup_flysystem.mount_manager')
            ->getFilesystem('frigadata');
        $arquivo = $filesystem->readStream($frigaArquivo->getNome());
        $temp = tmpfile();
        fwrite($temp,stream_get_contents($arquivo));
        $x = explode('/',$frigaArquivo->getNome());
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.end($x).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize(stream_get_meta_data($temp)['uri']));
        readfile(stream_get_meta_data($temp)['uri']);
        return  new Response();
    }
}