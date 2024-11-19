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

namespace Nte\UsuarioBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCargo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuarioConvite;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoPontuacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoProjetoParticipante;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoRecurso;
use Nte\Aplicacao\FrigaBundle\Form\FrigaRecursoCandidatoType;
use Nte\UsuarioBundle\Entity\Usuario;
use Nte\UsuarioBundle\Form\ConviteType;
use Nte\UsuarioBundle\Form\InscricaoProjetoType;
use Nte\UsuarioBundle\Form\InscricaoType;
use setasign\Fpdi\Tcpdf\Fpdi;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CandidatoController extends Controller
{
    use AssinaturaPDF;

    /**
     * Index dos usuários.
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $convites = $em->createQueryBuilder()
            ->select('c')
            ->from(FrigaEditalUsuarioConvite::class, 'c')
            ->where('c.cpf = :cpf')
            ->andWhere('c.aceite is null')
            ->setParameter('cpf', $this->getUser()->getCpf())
            ->getQuery()->getResult();

        return $this->render('@NteUsuario/Candidato/index.html.twig', [
            'convites' => $convites,
        ]);
    }

    /**
     * @return RedirectResponse
     *
     * @throws \Exception
     */
    public function cancelarInscricaoAction(Request $request, FrigaInscricao $inscricao)
    {
        if ($inscricao->getIdUsuario()->getId() == $this->getUser()->getId()) {
            if ($inscricao->getIdEdital()->getPeriodoInscricaoHabilitado()) {
                if (0 == $inscricao->getIdSituacao()) {
                    $inscricao->setIdSituacao(-999);
                    $this->getDoctrine()->getManager()->flush();
                    $this->addFlash('success', 'Inscrição cancelada com sucesso');
                } elseif ($inscricao->getIdSituacao() - 999) {
                    $this->addFlash('success', 'Inscrição anteriormente cancelada.');
                } else {
                    $this->addFlash('error', 'Esta inscrição não pode ser cancelada.');
                }
            } else {
                $this->addFlash('error', 'Esta inscrição não pode ser cancelada. Fora do período de inscrição');
            }
        } else {
            if ($this->isGranted('ROLE_ADMIN')) {
                $this->addFlash('success', 'xxxx');
            } else {
                $this->addFlash('error', 'Esta inscrição não pode ser cancelada. Você não tem permissão para cancelar esta inscrição');
            }
        }

        return $this->redirectToRoute('nte_usuario_candidato_inscricao_index');
    }

    /**
     * Inscrição concluída.
     *
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
     * Inscrição concluída.
     *
     * @return Response
     */
    public function inscricaoRealizadaAction(Request $request, FrigaInscricao $inscricao)
    {
        return $this->render('@NteUsuario/Candidato/ver-inscricao.html.twig', [
            'inscricao' => $inscricao,
        ]);
    }

    /**
     * Index inscrições do candidato.
     *
     * @return Response
     */
    public function indexInscricoesAction(Request $request)
    {
        return $this->render('@NteUsuario/Candidato/index-inscricao.html.twig', [
        ]);
    }

    /**
     * Index Recursos do candidato.
     *
     * @return Response
     */
    public function indexRecursosAction(Request $request)
    {
        return $this->render('@NteUsuario/Candidato/index-recurso.html.twig', [
        ]);
    }

    /**
     * @return BinaryFileResponse
     */
    public function gerarTermoAction(Request $request, FrigaEditalUsuarioConvite $convite)
    {
        $arquivo = '/tmp/termo.pdf';
        if (\is_file($arquivo)) {
            \unlink($arquivo);
        }
        $pdf = new Fpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15, 15, 15, 15);
        $pdf->SetAutoPageBreak(true, 40);
        //$pdf->SetFont('helvetica');
        $pdf->SetFont('dejavusans');
        $pdf->setFontSize('9px');
        // $pdf->SetTextColor(113, 102, 80);

        $html = '<br><br>';
        $html .= '<p  style="text-align:center">';
        $html .= '______________________________________<br>';
        $html .= \strtoupper($this->getUser()->getNome());
        $html .= '<br>';
        $html .= 'CPF: ' . $this->getUser()->getCpf();
        $html .= '</p>';
        $html .= '<style>div{font-size: 9px;}</style>';

        //$pdf->setHtmlVSpace($tagvs);
        $pdf->AddPage();
        $pdf->WriteHTML($this->renderView('@NteUsuario/Candidato/termo-sigilo.html.twig', [
            'edital' => $convite->getIdEdital(),
            'usuario' => $this->getUser(),
        ]));
        $pdf->WriteHTML($html, false, true, true, true, 'rigth');
        $pdf->Output($arquivo, 'F');

        return $this->file($arquivo);
    }

    /**
     * @return bool
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function checarAssinatura(FrigaEditalUsuarioConvite $convite)
    {
        if (!$convite->isAceite()) {
            return true;
        }
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $certs = [];

        /** @var FrigaArquivo $item */
        foreach ($convite->getIdArquivo() as $item) {
            $remover = false;
            $arquivo = '/media/frigadata/' . $item->getNome();
            $cert = $this->extract_pkcs7_signatures($arquivo);
            $declaracao = $item->isParseTextTipo(-1);
            if (\count($cert)) {
                $certs[] = $cert;
            } else {
                $remover = true;
                $this->addFlash('danger', 'Documento inválido - Não foi localizada uma assinatura digital no arquivo em anexo.');
            }
            if (!$declaracao) {
                $remover = true;
                $this->addFlash('danger', 'Documento inválido - O documento em anexo não corresponde ao termo de confidencialidade e sigilo');
            }
            if ($remover) {
                $convite->removeIdArquivo($item);
                $em->remove($item);
                $em->flush();
                \unlink($arquivo);
            }
        }
        if (\count($certs) and $declaracao) {
            return true;
        } else {
            $convite->setAceite(null)->setAceiteData(null);
            $em->persist($convite);
            $em->flush();
        }

        return false;
    }

    /**
     * Ver convite do candidato.
     *
     * @return Response
     */
    public function verConviteAction(Request $request, FrigaEditalUsuarioConvite $convite)
    {
        if (!\is_null($convite) and !\is_null($convite->getIdUsuario())) {
            $this->addFlash('danger', 'Convite respondido!');

            return $this->redirectToRoute('nte_usuario_candidato_convites_index');
        }
        if (!\is_null($convite->isAceite())) {
            $this->addFlash('danger', 'Convite respondido!');

            return $this->redirectToRoute('nte_usuario_candidato_convites_index');
        }
        if ($this->getUser()->getCpf() != $convite->getCpf()) {
            $this->addFlash('danger', 'Convite enviado para outro destinatário');

            return $this->redirectToRoute('nte_usuario_candidato_convites_index');
        }

        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(ConviteType::class, $convite);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $this->checarAssinatura($convite)) {
            /** @var Usuario $u */
            $u = $this->getUser();
            $convite->setIdUsuario($u);

            $em->persist($convite);
            $em->flush();
            $this->addFlash('success', 'Convite respondido com sucesso!');
            if ($convite->isAceite()) {
                $frigaEditalUsuario = new FrigaEditalUsuario();
                $frigaEditalUsuario->setIdUsuario($u)
                    //->setTermoCompromisso(0)
                    //->setTermoCompromissoData(new \DateTime())
                    ->setIdEdital($convite->getIdEdital())
                    ->setAdministrador($convite->isFuncaoAdministracao())
                    ->setAvaliador($convite->isFuncaoAvaliacao())
                    ->setResultado($convite->isFuncaoResultado())
                    ->setConvocacao($convite->isFuncaoConvocacao());

                /** @var FrigaEditalCargo $item */
                foreach ($convite->getIdEditalCargo() as $item) {
                    $item->addIdEditalUsuario($frigaEditalUsuario);
                }
                $em->persist($frigaEditalUsuario);
                $em->flush();

                $logout = false;
                if (!$this->isGranted('ROLE_AVALIADOR') and $convite->isFuncaoAvaliacao()) {
                    $u->addRole('ROLE_AVALIADOR');
                    $em->persist($u);
                    $em->flush();
                    $logout = true;
                }
                if (!$this->isGranted('ROLE_ADMIN_EDITAL') and $convite->isFuncaoAdministracao()) {
                    $u->addRole('ROLE_ADMIN_EDITAL');
                    $em->persist($u);
                    $em->flush();
                    $logout = true;
                }
                if ($logout) {
                    return $this->redirectToRoute('fos_user_security_logout');
                }

                return $this->redirectToRoute('nte_admin_painel_homepage');
            }

            return $this->redirectToRoute('nte_usuario_candidato_convites_index');
        }

        return $this->render('@NteUsuario/Candidato/form-convite.html.twig', [
            'form' => $form->createView(),
            'convite' => $convite,
        ]);
    }

    /**
     * Index Recursos do candidato.
     *
     * @return Response
     */
    public function indexConviteAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $convites = $em->createQueryBuilder()
            ->select('c')
            ->from(FrigaEditalUsuarioConvite::class, 'c')
            ->where('c.cpf = :cpf')
            ->setParameter('cpf', $this->getUser()->getCpf())
            ->getQuery()->getResult();

        return $this->render('@NteUsuario/Candidato/index-convite.html.twig', [
            'convites' => $convites,
        ]);
    }

    /**
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
            if (isset($arquivos) and \is_array($arquivos)) {
                foreach ($arquivos as $arquivo) {
                    $a = $em->find(FrigaArquivo::class, $arquivo);
                    if (!$a) {
                        $message = \Swift_Message::newInstance()
                            ->setSubject('Arquivo não encontrado. - ' . $inscricao->getUuid())
                            ->setBcc(['luizguilherme@nte.ufsm.br'])
                            ->setFrom('processoseletivo@nte.ufsm.br', 'Processo Seletivo')
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
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return Response
     */
    public function verRecursoAction(Request $request, FrigaInscricaoRecurso $recurso)
    {
        return $this->render('@NteUsuario/Candidato/ver-recurso.html.twig', [
            'recurso' => $recurso,
        ]);
    }

    /**
     * Index Convocações do candidato.
     *
     * @return Response
     */
    public function indexConvocacaoAction(Request $request)
    {
        return $this->render('@NteUsuario/Candidato/index-convocacao.html.twig', [
        ]);
    }

    /**
     * Index editais abertos.
     *
     * @return Response
     */
    public function indexEditaisAction(Request $request)
    {
        return $this->render('@NteUsuario/Candidato/index-edital.html.twig', [
        ]);
    }

    /**
     * Index editais abertos.
     *
     * @return Response
     */
    public function indexPerfilAction(Request $request)
    {
        return $this->render('@NteUsuario/Candidato/index-perfil.html.twig', [
        ]);
    }

    /**
     * Index editais abertos.
     *
     * @return Response
     */
    public function indexResultadoAction(Request $request)
    {
        return $this->render('@NteUsuario/Candidato/index-resultado.html.twig', [
        ]);
    }

    /**
     * @return RedirectResponse|Response
     *
     * @throws \Exception
     */
    public function inscricaoProjetoAction(Request $request, FrigaInscricaoProjetoParticipante $inscricao)
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
            if (isset($arquivos) and \is_array($arquivos)) {
                foreach ($arquivos as $arquivo) {
                    $a = $em->find(FrigaArquivo::class, $arquivo);
                    if (!$a) {
                        $message = \Swift_Message::newInstance()
                            ->setSubject('Arquivo não encontrado. - ' . $inscricao->getUuid())
                            ->setBcc(['luizguilherme@nte.ufsm.br'])
                            ->setFrom('processoseletivo@nte.ufsm.br', 'Processo Seletivo')
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
            'form' => $form->createView(),
        ]);
    }

    public function invalidarInscriaoQuery($param)
    {
    }

    public function invalidarInscriao($param)
    {
    }

    /**
     * @return RedirectResponse|Response
     *
     * @throws \Exception
     */
    public function inscricaoAction(Request $request, FrigaEdital $edital)
    {
        if (!(1 == $edital->getPublico() && $edital->getPeriodoInscricao()->getAndamentoPrazo() > 0)
            or !(2 == $edital->getPublico() && $edital->getPeriodoInscricao()->getAndamentoPrazo() < 100)
            or !(3 == $edital->getPublico())
            or !$this->isGranted('ROLE_ADMIN')
        ) {
            // return $this->redirectToRoute('nte_site_homepage');
        }
        if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
            if (\is_null($edital->getPeriodoInscricaoAtual())) {
                return $this->redirectToRoute('nte_site_edital', [
                    'id' => $edital->getId(), 'url' => $edital->getUrl(),
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
            ->setIdEtapa($edital->getPeriodoInscricaoAtual())
            ->setIdSituacao(0);

        $form = $this->createForm(InscricaoType::class, $inscricao, ['entityManager' => $this->getDoctrine()->getManager()]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            if (1 == $edital->getModeloInscricao()) {
                if (\is_array($inscricao->getProjetoAreaConhecimento())) {
                    $inscricao->setProjetoAreaConhecimento(\implode(',', $inscricao->getProjetoAreaConhecimento()));
                }
            }

            try {
                $arquivos = $request->request->get('arquivos');

                /** @var QueryBuilder $inscricaoAnterior */
                $inscricaoAnterior = $em->createQueryBuilder()
                    ->update(FrigaInscricao::class, 'i')
                    ->set('i.idSituacao', -999)
                    ->where('i.idEdital = :edital and i.idUsuario  = :usuario ')
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
                    case 5: //Inscrição múltipla/Lista limitado
                        $iaAnterior = clone $inscricaoAnterior;
                        if (4 == $edital->getTipoInscricao()) {
                            $iaAnterior->andWhere('i.idCargo = :cargo')
                                ->setParameter('cargo', $inscricao->getIdCargo())
                                ->getQuery()
                                ->execute();
                        }
                        if (5 == $edital->getTipoInscricao()) {
                            $iaAnterior->andWhere('i.idCota = :cota')
                                ->setParameter('cota', $inscricao->getIdCota())
                                ->getQuery()
                                ->execute();
                        }
                        $ias = new ArrayCollection($em->createQueryBuilder()
                            ->select('i')
                            ->from(FrigaInscricao::class, 'i')
                            ->where('i.idEdital = :edital and i.idUsuario  = :usuario ')
                            ->andWhere('i.idSituacao > -999')
                            ->setParameter('edital', $edital)
                            ->setParameter('usuario', $this->getUser())
                            ->orderBy('i.id', 'ASC')
                            ->getQuery()
                            ->getResult());

                        /** @var FrigaEdital $item */
                        foreach ($ias as $item) {
                            if ($ias->count() < $edital->getTipoInscricaoLimite()) {
                                break;
                            }
                            $inscricaoAnterior->andWhere('i.id = :inscricao')
                                ->setParameter('inscricao', $item->getId())
                                ->getQuery()
                                ->execute();
                            $ias->removeElement($item);
                        }
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
                    if (0 === \strpos($chave, 'pt__') or 0 === \strpos($chave, 'cat__')) {
                        $pontuacao = new FrigaInscricaoPontuacao();
                        $pontuacao->setIdInscricao($inscricao)
                            ->setIdEditalEtapa($edital->getPeriodoInscricao());

                        // Captura o valor da pontuação
                        if (0 === \strpos($chave, 'pt__')) {
                            $idPontuacao = \intval(\str_replace('pt__', '', $chave));
                            $pt = $em->find(FrigaEditalPontuacao::class, $idPontuacao);
                            $pontuacao
                                ->setIdEditalPontuacao($pt)
                                ->setValorInformado(\floatval($valor));
                        }
                        // Captura o valor da pontuação através da categoria
                        if (0 === \strpos($chave, 'cat__')) {
                            $idCategoria = \intval(\str_replace('cat__', '', $chave));
                            $idPontuacao = \intval(\str_replace('pt__', '', \intval($valor)));
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
                    if (0 === \strpos($chave, 'tpt__') or 0 === \strpos($chave, 'tcat__')) {
                        if (\key_exists(\ltrim($chave, $chave[0]), $pontuacaoChave)) {
                            $pontuacaoChave[\ltrim($chave, $chave[0])]->setValorTexto($valor);
                            $em->persist($pontuacaoChave[\ltrim($chave, $chave[0])]);
                            $em->flush();
                        }
                    }
                }
                $projetoParticipante = [];
                if (1 == $edital->getModeloInscricao()) {
                    $projetoParticipante[0]['nome'] = $inscricao->getNome();
                    $projetoParticipante[0]['email'] = $inscricao->getContatoEmail();
                    $projetoParticipante[0]['usuario'] = $this->getUser();
                    foreach ($request->request->get('nte_inscricao') as $chave => $valor) {
                        if (0 === \strpos($chave, 'projetoParticipanteNome') and '' != $valor) {
                            $pn = \intval(\str_replace('projetoParticipanteNome', '', $chave));
                            $projetoParticipante[$pn]['nome'] = $valor;
                        }
                        if (0 === \strpos($chave, 'projetoParticipanteEmail') and '' != $valor) {
                            $pn = \intval(\str_replace('projetoParticipanteEmail', '', $chave));
                            $projetoParticipante[$pn]['email'] = $valor;
                            $projetoParticipante[$pn]['usuario'] = $em->getRepository(Usuario::class)->findOneByEmail($valor);
                        }
                    }
                    $ix = 0;
                    foreach ($projetoParticipante as $p) {
                        $pp = new FrigaInscricaoProjetoParticipante();
                        $pp->setNome($p['nome'])
                            ->setEmail($p['email'])
                            ->setIdUsuario($p['usuario'])
                            ->setIdInscricao($inscricao);
                        if (0 == $ix) {
                            $pp->setConfirmacao(1);
                        }
                        $em->persist($pp);
                        $em->flush();
                        if ($ix > 0) {
                            $this->enviarEmailPP($pp);
                        }
                        ++$ix;
                    }
                }

                if (isset($arquivos) and \is_array($arquivos)) {
                    $arquivos = \array_unique($arquivos);
                    foreach ($arquivos as $arquivo) {
                        try {
                            $a = $em->find(FrigaArquivo::class, $arquivo);
                            if (!$a) {
                                $message = \Swift_Message::newInstance()
                                    ->setSubject('Arquivo não encontrado. - ' . $inscricao->getUuid())
                                    ->setBcc(['alexandre@nte.ufsm.br', 'luizguilherme@nte.ufsm.br'])
                                    ->setFrom('processoseletivo@nte.ufsm.br', 'Processo Seletivo')
                                    ->setBody("Arquivo ID: {$arquivo} não encontrado");

                                $this->get('mailer')
                                    ->send($message);
                                continue;
                            }
                            if (\array_key_exists($a->getIdContexto(), $pontuacaoSalva)) {
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
                        } catch (\Exception $e) {
                            $message = \Swift_Message::newInstance()
                                ->setSubject('ERRO com a Inscrição|Anexo de arquivo  - ' . $inscricao->getUuid())
                                ->setBcc(['alexandre@nte.ufsm.br', 'luizguilherme@nte.ufsm.br'])
                                ->setFrom('processoseletivo@nte.ufsm.br', 'Processo Seletivo')
                                ->setBody($e->getMessage() . "\n" . \json_encode($arquivos));
                            $this->get('mailer')->send($message);

                            continue;
                        }
                    }
                }
            } catch (\Exception $e) {
                $message = \Swift_Message::newInstance()
                    ->setSubject('ERRO com a Inscrição - ' . $inscricao->getUuid())
                    ->setBcc(['alexandre@nte.ufsm.br', 'luizguilherme@nte.ufsm.br'])
                    ->setFrom('processoseletivo@nte.ufsm.br', 'Processo Seletivo')
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
     * @return Response
     */
    public function testeAction(Request $request, FrigaInscricao $inscricao)
    {
        $this->enviarEmail($inscricao);

        return $this->render('@NteUsuario/Candidato/email.html.twig', [
            'inscricao' => $inscricao,
        ]);
    }

    private function enviarEmail(FrigaInscricao $inscricao)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Inscrição - ' . $inscricao->getUuid())
            ->setFrom('processoseletivo@nte.ufsm.br', 'Processo Seletivo')
            ->setTo($inscricao->getContatoEmail())
            ->setBcc(['alexandre@nte.ufsm.br', 'luizguilherme@nte.ufsm.br'])
            ->setBody($this->renderView('@NteUsuario/Candidato/email.html.twig', [
                'inscricao' => $inscricao,
            ]), 'text/html');
        $this->get('mailer')->send($message);
    }

    private function enviarEmailPP(FrigaInscricaoProjetoParticipante $inscricao)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Inscrição em projeto - ' . $inscricao->getUuid())
            ->setFrom('processoseletivo@nte.ufsm.br', 'Processo Seletivo')
            ->setTo($inscricao->getEmail())
            //   ->setBcc(['alexandre@nte.ufsm.br', 'luizguilherme@nte.ufsm.br'])
            ->setBody($this->renderView('@NteUsuario/Candidato/email.pp.html.twig', [
                'inscricao' => $inscricao,
            ]), 'text/html');
        $this->get('mailer')->send($message);
        //dump($message);
    }

    /**
     * @param null $uuid
     * @param null $arquivo
     *
     * @return Response
     */
    public function downloadArquivoAction(Request $request, $uuid = null, $arquivo = null)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var FrigaInscricaoProjetoParticipante|null $inscricacao */
        $inscricacao = $em->getRepository(FrigaInscricaoProjetoParticipante::class)->findOneByUuid($uuid);

        if (\is_null($inscricacao) or \is_null($arquivo)) {
            return new Response();
        }
        $frigaArquivo = $inscricacao->getIdInscricao()->getIdArquivo()->filter(function(FrigaArquivo $a) use ($arquivo) {
            return $a->getId() == $arquivo;
        })->first();
        if (\is_null($frigaArquivo)) {
            return new Response();
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

    private function assinatura()
    {
        $texto = '<b>DOCUMENTO ASSINADO ELETRONICAMENTE</b>';
        $texto .= '<br><b>Nome:</b> ' . $certificado->getInfo0();
        $texto .= '<br><b>Cidade:</b> ' . $certificado->getInfo3();
        $texto .= '<br><b>Certificadora:</b> ' . $certificado->getInfo4();
        $texto .= '<br><b>Data:</b> ' . \date('d/m/Y H:i:s');
        if ($uuid) {
            $texto .= '<br><b>UUID:</b> ' . $uuid;
        }
        //        $pdf->setSignature($certificado->getCertificado(), $certificado->getChave(), '', $raiz, 2, $info);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->AddPage();
        $pdf->SetMargins(65, 25, 20);
        $pdf->SetXY(65, 127);
        $pdf->writeHTML($texto, true, 0, true, 0);
        $style = [
            'border' => 0,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => [0, 0, 0],
            'bgcolor' => false,
            'module_width' => 1,
            'module_height' => 1,
        ];
        $url = '';
        //$url = $this->generateUrl('nte_documental_verificador', ['uuid' => $uuid], 0);
        $pdf->write2DBarcode($url, 'QRCODE,H', 25, 120, 40, 40, $style, 'N', true);
        $pdf->setSignatureAppearance(25, 120, 40, 40);
        $pdf->addEmptySignatureAppearance(25, 120, 40, 40);

        return $pdf;
    }
}
