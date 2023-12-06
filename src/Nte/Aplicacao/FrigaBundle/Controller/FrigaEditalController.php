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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaConvocacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCargo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCategoria;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCota;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalDesempate;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapaCategoria;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacaoCategoria;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuarioConvite;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoPontuacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoRecurso;
use Nte\Aplicacao\FrigaBundle\Entity\Log;
use Nte\Aplicacao\FrigaBundle\Form\FrigaAvaliadorCargoType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaAvaliadorConviteType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaAvaliadorType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalCargoType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalCategoriaType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalConfigInscricaoType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalCotaType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalDesempateType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalEtapaCategoriaType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalEtapaType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalExportadorType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalPontuacaoCategoriaPesoType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalPontuacaoCategoriaType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalPontuacaoType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalResultadoType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalTermoType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalType;
use Nte\UsuarioBundle\Entity\Usuario;
use setasign\Fpdi\Tcpdf\Fpdi;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * frigaedital controller.
 */
class FrigaEditalController extends Controller
{
    /**
     * Lists all frigaedital entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $frigaEditais = $em->getRepository(FrigaEdital::class)
            ->createQueryBuilder('e')
            ->where('e.situacao = 1')
            ->orderBy('e.registroDataCriacao', 'desc')
            ->getQuery()
            ->getResult();

        return $this->render('NteAplicacaoFrigaBundle:edital:index.html.twig', [
            'editais' => $frigaEditais,
        ]);
    }

    /**
     * Lists all frigaedital entities.
     */
    public function indexRascunhoAction()
    {
        $em = $this->getDoctrine()->getManager();

        $frigaEditais = $em->getRepository(FrigaEdital::class)
            ->createQueryBuilder('e')
            ->where('e.situacao = 0')
            ->orderBy('e.registroDataCriacao', 'desc')
            ->getQuery()
            ->getResult();

        return $this->render('NteAplicacaoFrigaBundle:edital:index.html.twig', [
            'editais' => $frigaEditais,
        ]);
    }

    /**
     * Lists all frigaedital entities.
     */
    public function indexEncerradoAction()
    {
        $em = $this->getDoctrine()->getManager();

        $frigaEditais = $em->getRepository(FrigaEdital::class)->createQueryBuilder('e')
            ->where('e.situacao = 2')
            ->orderBy('e.registroDataCriacao', 'desc')
            ->getQuery()
            ->getResult();

        return $this->render('NteAplicacaoFrigaBundle:edital:index.html.twig', [
            'editais' => $frigaEditais,
        ]);
    }

    /**
     * @return Response
     */
    public function indexArquivoAction(Request $request, FrigaEdital $frigaEdital)
    {
        $form = $this->createFormBuilder()
            ->add('arquivo', FileType::class, ['label' => 'Arquivo PDF'])
            ->add('certificado', FileType::class, ['label' => 'Certificado'])
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

        return $this->render('NteAplicacaoFrigaBundle:edital:form-arquivo.html.twig', [
            'frigaedital' => $frigaEdital,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return Response
     */
    public function indexCargoAction(Request $request, FrigaEdital $frigaEdital)
    {
        return $this->render('NteAplicacaoFrigaBundle:edital:index-cargo.html.twig', [
            'frigaedital' => $frigaEdital,
        ]);
    }

    /**
     * @return Response
     */
    public function indexCategoriaAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categoria = $em->getRepository(FrigaEditalCategoria::class)->findAll();

        return $this->render('NteAplicacaoFrigaBundle:edital:index-categoria.html.twig', [
            'categoria' => $categoria,
        ]);
    }

    /**
     * @return RedirectResponse|Response
     */
    public function formCategoriaAction(Request $request, FrigaEditalCategoria $categoria = null)
    {
        if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }

        if (!$categoria) {
            $categoria = new FrigaEditalCategoria();
        }

        $form = $this->createForm(FrigaEditalCategoriaType::class, $categoria);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($categoria);
            $em->flush();
            $this->addFlash('info', 'Categoria salva com sucesso!');
            if ($request->query->get('uuid')) {
                return $this->redirectToRoute('edital_editar', ['uuid' => $request->query->get('uuid')]);
            } else {
                return $this->redirectToRoute('edital_categoria_index');
            }
        }

        return $this->render('NteAplicacaoFrigaBundle:edital:form-categoria.html.twig', [
            'categoria' => $categoria,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function removerCategoriaAction(Request $request, FrigaEditalCategoria $categoria)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($categoria);
            $em->flush();
            $this->addFlash('info', 'Categoria removida com sucesso');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Erro ao remover categoria');
        }

        return $this->redirectToRoute('edital_categoria_index');
    }

    /**
     * @return RedirectResponse|Response
     */
    public function formCargoAction(Request $request, FrigaEdital $frigaEdital, FrigaEditalCargo $cargo = null)
    {
        if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }

        if (!$cargo) {
            $cargo = new FrigaEditalCargo();
            $cargo->setIdEdital($frigaEdital);
        }

        $form = $this->createForm(FrigaEditalCargoType::class, $cargo);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cargo);
            $em->flush();
            $this->addFlash('info', 'Cargo salvo com sucesso!');

            return $this->redirectToRoute('edital_cargo', ['uuid' => $frigaEdital->getUuid()]);
        }

        return $this->render('NteAplicacaoFrigaBundle:edital:form-cargo.html.twig', [
            'frigaeditalcargo' => $cargo,
            'frigaedital' => $frigaEdital,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function removerCargoAction(Request $request, FrigaEdital $frigaedital, FrigaEditalCargo $cargo)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($cargo);
            $em->flush();
            $this->addFlash('info', 'Cargp removido com sucesso!');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Erro ao remover cargo');
        }

        return $this->redirectToRoute('edital_cargo', ['uuid' => $frigaedital->getUuid()]);
    }

    /**
     * @return Response
     */
    public function indexCotaAction(Request $request, FrigaEdital $frigaEdital)
    {
        return $this->render('NteAplicacaoFrigaBundle:edital:index-cota.html.twig', [
            'frigaedital' => $frigaEdital,
        ]);
    }

    /**
     * @return RedirectResponse|Response
     */
    public function formCotaAction(Request $request, FrigaEdital $frigaEdital, FrigaEditalCota $cota = null)
    {
        if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }

        if (!$cota) {
            $cota = new FrigaEditalCota();
            $cota->setIdEdital($frigaEdital);
        }

        $form = $this->createForm(FrigaEditalCotaType::class, $cota);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cota);
            $em->flush();
            $this->addFlash('info', 'Lista salva com sucesso!');

            return $this->redirectToRoute('edital_cota', ['uuid' => $frigaEdital->getUuid()]);
        }

        return $this->render('NteAplicacaoFrigaBundle:edital:form-cota.html.twig', [
            'frigaeditalcota' => $cota,
            'frigaedital' => $frigaEdital,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function removerCotaAction(Request $request, FrigaEdital $frigaedital, FrigaEditalCota $cota)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($cota);
            $em->flush();
            $this->addFlash('info', 'Lista removida com sucesso!');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Erro ao remover lista!');
        }

        return $this->redirectToRoute('edital_cota', ['uuid' => $frigaedital->getUuid()]);
    }

    /**
     * @return Response
     */
    public function indexAvaliadorAction(Request $request, FrigaEdital $frigaEdital)
    {
        return $this->render('NteAplicacaoFrigaBundle:edital:index-avaliador.html.twig', [
            'frigaedital' => $frigaEdital,
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function apiAvaliadorListaAction(Request $request, FrigaEdital $frigaEdital)
    {
        $tmp = [];
        $obj = new \stdClass();
        $obj->id = -1;
        $obj->text = '--Avaliador--';
        $tmp[] = $obj;
        /** @var FrigaEditalUsuario $item */
        foreach ($frigaEdital->getIdEditalUsuario() as $item) {
            if (!$item->isAvaliador()) {
                continue;
            }
            $obj = new \stdClass();
            $obj->id = $item->getIdUsuario()->getId();
            $obj->text = $item->getIdUsuario()->getNome();
            $tmp[] = $obj;
        }

        return $this->json($tmp);
    }

    /**
     * @return Response
     */
    public function criarAvaliadorAction(Request $request, FrigaEdital $frigaEdital, FrigaEditalUsuario $avaliador = null)
    {
        if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }

        $em = $this->getDoctrine()->getManager();

        if (!$avaliador) {
            $avaliador = new FrigaEditalUsuario();
            $avaliador->setIdEdital($frigaEdital);
        }

        $configForm = $this->createForm(FrigaAvaliadorType::class, $avaliador);
        $configForm->handleRequest($request);

        if ($configForm->isSubmitted() && $configForm->isValid()) {
            $em->persist($avaliador);
            $em->flush();
            $this->addFlash('info', 'A banca foi configurada com sucesso!');

            return $this->redirectToRoute('edital_avaliador', ['uuid' => $frigaEdital->getUuid()]);
        }

        return $this->render('NteAplicacaoFrigaBundle:edital:form-avaliador.html.twig', [
            'frigaedital' => $frigaEdital,
            'frigaeditalusuario' => $avaliador,
            'form' => $configForm->createView(),
        ]);
    }

    /**
     * @return Response
     */
    public function criarConviteAction(Request $request, FrigaEdital $frigaEdital, FrigaEditalUsuarioConvite $convite = null)
    {
        if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }
        $em = $this->getDoctrine()->getManager();
        if (\is_null($convite)) {
            $convite = new FrigaEditalUsuarioConvite();
            $convite->setIdEdital($frigaEdital);
        }
        $configForm = $this->createForm(FrigaAvaliadorConviteType::class, $convite);
        $configForm->handleRequest($request);

        if ($configForm->isSubmitted() && $configForm->isValid()) {
            $em->persist($convite);
            $em->flush();
            $this->addFlash('info', 'O convite foi configurado com sucesso!');

            return $this->redirectToRoute('edital_avaliador', ['uuid' => $frigaEdital->getUuid()]);
        }

        return $this->render('NteAplicacaoFrigaBundle:edital:form-avaliador-convite.html.twig', [
            'frigaedital' => $frigaEdital,
            'convite' => $convite,
            'form' => $configForm->createView(),
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function enviarConviteAction(Request $request, FrigaEdital $frigaedital, FrigaEditalUsuarioConvite $convite)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        try {
            $message = \Swift_Message::newInstance()
                ->setSubject('Convite para participação de banca ')
                ->setFrom('processoseletivo@nte.ufsm.br', 'Processo Seletivo')
                ->setTo($convite->getEmail())
                ->setBcc(['alexandre@nte.ufsm.br', 'luizguilherme@nte.ufsm.br'])
                ->setBody($this->renderView('@NteAplicacaoFriga/Notificacao/msg-avaliador-convite.html.twig', [
                    'edital' => $frigaedital,
                    'convite' => $convite,
                ]), 'text/html');

            $this->get('mailer')->send($message);

            $convite->setEnvio(true)->setEnvioData(new \DateTime());
            $em->persist($convite);
            $em->flush();
        } catch (\Exception $exception) {
            dump($exception->getMessage());
        }

        return $this->redirectToRoute('edital_avaliador', ['uuid' => $frigaedital->getUuid()]);
    }

    /**
     * @return RedirectResponse
     */
    public function removerConviteAction(Request $request, FrigaEdital $frigaedital, FrigaEditalUsuarioConvite $convite)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($convite);
            $em->flush();
        } catch (\Exception $e) {
        }

        return $this->redirectToRoute('edital_avaliador', ['uuid' => $frigaedital->getUuid()]);
    }

    /**
     * @return RedirectResponse
     */
    public function removerAvaliadorAction(Request $request, FrigaEdital $frigaedital, FrigaEditalUsuario $avaliador)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($avaliador);
            $em->flush();
        } catch (\Exception $e) {
        }

        return $this->redirectToRoute('edital_avaliador', ['uuid' => $frigaedital->getUuid()]);
    }

    /**
     * @return Response
     */
    public function adicionarAvaliadorCargoAction(Request $request, FrigaEdital $frigaEdital, Usuario $usuario)
    {
        if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }

        $em = $this->getDoctrine()->getManager();
        $configForm = $this->createForm(FrigaAvaliadorCargoType::class, $usuario);
        $configForm->handleRequest($request);

        if ($configForm->isSubmitted() && $configForm->isValid()) {
            $frigaEdital->getIdUsuario();
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('info', 'Os avalidores foram adicionados com sucesso ao edital');

            return $this->redirectToRoute('edital_avaliador', ['uuid' => $frigaEdital->getUuid()]);
        }

        return $this->render('NteAplicacaoFrigaBundle:edital:form-avaliador-cargo.html.twig', [
            'frigaedital' => $frigaEdital,
            'config_form' => $configForm->createView(),
        ]);
    }

    /**
     * @return Response
     */
    public function indexDesempateAction(Request $request, FrigaEdital $frigaEdital)
    {
        return $this->render('NteAplicacaoFrigaBundle:edital:index-desempate.html.twig', [
            'frigaedital' => $frigaEdital,
        ]);
    }

    public function ordemDesempateAction(Request $request, FrigaEdital $frigaEdital, FrigaEditalDesempate $criterio, $tipo)
    {
        $tmp = [];
        $em = $this->getDoctrine()->getManager();
        /** @var FrigaEditalDesempate $p */
        foreach ($frigaEdital->getDesempate() as $p) {
            $tmp[$p->getPosicao()] = $p;
        }
        if ($tipo) {
            if (\array_key_exists($criterio->getPosicao() + 1, $tmp)) {
                $x = $tmp[$criterio->getPosicao() + 1];
                $tmp[$criterio->getPosicao() + 1] = $tmp[$criterio->getPosicao()];
                $tmp[$criterio->getPosicao()] = $x;
            }
        } else {
            if (\array_key_exists($criterio->getPosicao() - 1, $tmp)) {
                $x = $tmp[$criterio->getPosicao() - 1];
                $tmp[$criterio->getPosicao() - 1] = $tmp[$criterio->getPosicao()];
                $tmp[$criterio->getPosicao()] = $x;
            }
        }
        foreach ($tmp as $indice => $valor) {
            $valor->setPosicao($indice);
            $em->persist($valor);
        }
        $em->flush();

        return new Response();
    }

    /**
     * @param int $tipo
     *
     * @return RedirectResponse
     *
     * @throws \Exception
     */
    public function criarDesempateAction(Request $request, FrigaEdital $frigaEdital, $tipo = 1)
    {
        if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }
        $em = $this->getDoctrine()->getManager();

        $desempate = new ArrayCollection($em
            ->getRepository(FrigaEditalDesempate::class)
            ->findBy(['idEdital' => $frigaEdital])
        );

        $entidade = new FrigaEditalDesempate();
        $entidade->setIdEdital($frigaEdital);
        $entidade->setPosicao($desempate->count() + 1);
        $entidade->setTipo(0);
        switch ($tipo) {
            case 1:
                $entidade->setContexto(FrigaEditalPontuacao::class);
                break;
            case 2:
                $entidade->setContexto(FrigaEditalPontuacaoCategoria::class);
                break;
            case 3:
                $entidade->setTipo(1);
                $entidade->setContexto(FrigaInscricao::class);
                $entidade->setPropriedade('dataNascimento');
                break;
            case 4:
                $entidade->setContexto(FrigaInscricao::class);
                $entidade->setPropriedade('nome');
                break;
            case 5:
                $entidade->setContexto(FrigaInscricao::class);
                $entidade->setPropriedade('matriculaIndiceDesempenho');
                break;
        }
        $em->persist($entidade);
        $em->flush();

        return $this->redirectToRoute('edital_desempate_editar', ['uuid' => $frigaEdital->getUuid(), 'criterio' => $entidade->getId()]);
    }

    /**
     * @param FrigaEditalDesempate|null $criterio
     *
     * @return Response
     */
    public function formDesempateAction(Request $request, FrigaEdital $frigaEdital, FrigaEditalDesempate $criterio)
    {
        $this->getUser();
        $em = $this->getDoctrine()->getManager();

        if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }

        $form = $this->createForm(FrigaEditalDesempateType::class, $criterio, ['em' => $em]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($criterio);
            $em->flush();

            return $this->redirectToRoute('edital_desempate', ['uuid' => $frigaEdital->getUuid()]);
        }

        return $this->render('NteAplicacaoFrigaBundle:edital:form-desempate.html.twig', [
            'frigaeditaledesempate' => $criterio,
            'frigaedital' => $frigaEdital,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function removerDesempateAction(Request $request, FrigaEdital $frigaedital, FrigaEditalDesempate $criterio)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($criterio);
            $em->flush();
        } catch (\Exception $e) {
        }

        return $this->redirectToRoute('edital_desempate', ['uuid' => $frigaedital->getUuid()]);
    }

    /**
     * @return Response
     */
    public function indexEtapaAction(Request $request, FrigaEdital $frigaEdital)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $editais = $em->createQueryBuilder()
            ->select('e.id')
            ->addSelect('e.titulo')
            ->from(FrigaEdital::class, 'e')
            ->where('e.id != :id')
            ->setParameter('id', $frigaEdital->getId())
            ->orderBy('e.id', 'desc')
            ->getQuery()->getResult();
        $editais = \array_map(function($e) {
            return (object) $e;
        }, $editais);

        return $this->render('NteAplicacaoFrigaBundle:edital:index-etapa.html.twig', [
            'frigaedital' => $frigaEdital,
            'editais' => $editais,
        ]);
    }

    /**
     * @param int $tipo
     *
     * @return RedirectResponse
     *
     * @throws \Exception
     */
    public function criarEtapaAction(Request $request, FrigaEdital $frigaEdital, $tipo = 1)
    {
        if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }
        $etapa = new FrigaEditalEtapa();
        $etapa->setIdEdital($frigaEdital)->setTipo($tipo);
        $em = $this->getDoctrine()->getManager();
        $em->persist($etapa);
        $em->flush();

        return $this->redirectToRoute('edital_etapa_editar', ['uuid' => $frigaEdital->getUuid(), 'etapa' => $etapa->getId()]);
    }

    /**
     * @return Response
     */
    public function formEtapaAction(Request $request, FrigaEdital $frigaEdital, FrigaEditalEtapa $etapa = null)
    {
        $this->getUser();
        if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }
        if (!$etapa) {
            $etapa = new FrigaEditalEtapa();
            $etapa->setIdEdital($frigaEdital);
        }
        $etapa->getDataFinal()->setTime(0, 0, 0);
        $form = $this->createForm(FrigaEditalEtapaType::class, $etapa);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $etapa->getDataFinal()->setTime(23, 59, 59);
            $em->persist($etapa);
            $em->flush();

            return $this->redirectToRoute('edital_etapa', ['uuid' => $frigaEdital->getUuid()]);
        }

        return $this->render('NteAplicacaoFrigaBundle:edital:form-etapa.html.twig', [
            'frigaeditaletapa' => $etapa,
            'frigaedital' => $frigaEdital,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function removerEtapaAction(Request $request, FrigaEdital $frigaedital, FrigaEditalEtapa $etapa)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($etapa);
            $em->flush();
        } catch (\Exception $e) {
        }

        return $this->redirectToRoute('edital_etapa', ['uuid' => $frigaedital->getUuid()]);
    }

    /**
     * @return RedirectResponse
     *
     * @throws \Exception
     */
    public function criarEtapaCategoriaAction(Request $request, FrigaEdital $frigaEdital)
    {
        if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }
        $cat = new FrigaEditalEtapaCategoria();
        $cat->setDescricao('Nova categoria')
            ->setIdEdital($frigaEdital)
            ->setOrdem($frigaEdital->getIdEtapaCategoria()->count() + 1);
        $em = $this->getDoctrine()->getManager();
        $em->persist($cat);
        $em->flush();

        return $this->redirectToRoute('edital_etapa_categoria_editar', ['uuid' => $frigaEdital->getUuid(), 'categoria' => $cat->getId()]);
    }

    /**
     * @return Response
     */
    public function formEtapaCategoriaAction(Request $request, FrigaEdital $frigaEdital, FrigaEditalEtapaCategoria $categoria = null)
    {
        $this->getUser();
        if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }
        if (!$categoria) {
            $categoria = new FrigaEditalEtapaCategoria();
            $categoria->setIdEdital($frigaEdital);
        }
        $form = $this->createForm(FrigaEditalEtapaCategoriaType::class, $categoria);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($categoria);
            $em->flush();

            return $this->redirectToRoute('edital_etapa', ['uuid' => $frigaEdital->getUuid()]);
        }

        return $this->render('NteAplicacaoFrigaBundle:edital:form-etapa-categoria.html.twig', [
            'frigaeditaletapacategoria' => $categoria,
            'frigaedital' => $frigaEdital,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function removerEtapaCategoriaAction(Request $request, FrigaEdital $frigaedital, FrigaEditalEtapaCategoria $categoria)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($categoria);
            $em->flush();
        } catch (\Exception $e) {
        }

        return $this->redirectToRoute('edital_etapa', [
            'uuid' => $frigaedital->getUuid(),
        ]);
    }

    /**
     * @return Response
     */
    public function indexPontuacaoAction(Request $request, FrigaEdital $frigaEdital)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $editais = $em->createQueryBuilder()
            ->select('e.id')
            ->addSelect('e.titulo')
            ->from(FrigaEdital::class, 'e')
            ->where('e.id != :id')
            ->setParameter('id', $frigaEdital->getId())
            ->orderBy('e.id', 'desc')
            ->getQuery()->getResult();
        $editais = \array_map(function($e) {
            return (object) $e;
        }, $editais);

        return $this->render('NteAplicacaoFrigaBundle:edital:index-pontuacao.html.twig', [
            'frigaedital' => $frigaEdital,
            'editais' => $editais,
        ]);
    }

    /**
     * @return RedirectResponse|Response
     */
    public function formPontuacaoAction(Request $request, FrigaEdital $frigaEdital, FrigaEditalPontuacao $pontuacao = null)
    {
        $this->getUser();
        if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }
        if (!$pontuacao) {
            $pontuacao = new FrigaEditalPontuacao();
            $pontuacao->setIdEdital($frigaEdital);
        }
        $form = $this->createForm(FrigaEditalPontuacaoType::class, $pontuacao);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pontuacao);
            $em->flush();

            return $this->redirectToRoute('edital_pontuacao', ['uuid' => $frigaEdital->getUuid()]);
        }

        return $this->render('NteAplicacaoFrigaBundle:edital:form-pontuacao.html.twig', [
            'frigaeditalpontuacao' => $pontuacao,
            'frigaedital' => $frigaEdital,
            'form' => $form->createView(),
        ]);
    }

    public function removerPontuacaoAction(Request $request, FrigaEdital $frigaedital, FrigaEditalPontuacao $pontuacao)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($pontuacao);
            $em->flush();
        } catch (\Exception $e) {
        }

        return $this->redirectToRoute('edital_pontuacao', ['uuid' => $frigaedital->getUuid()]);
    }

    /**
     * @return RedirectResponse|Response
     */
    public function formPontuacaoCategoriaPesoAction(Request $request, FrigaEdital $frigaEdital, FrigaEditalPontuacaoCategoria $pontuacaoCategoria = null)
    {
        $this->getUser();
        if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }

        if (!$pontuacaoCategoria) {
            $pontuacaoCategoria = new FrigaEditalPontuacaoCategoria();
            $pontuacaoCategoria->setIdEdital($frigaEdital);
        }

        $form = $this->createForm(FrigaEditalPontuacaoCategoriaPesoType::class, $pontuacaoCategoria);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pontuacaoCategoria);
            $em->flush();

            return $this->redirectToRoute('edital_pontuacao', ['uuid' => $frigaEdital->getUuid()]);
        }

        return $this->render('NteAplicacaoFrigaBundle:edital:form-pontuacao-categoria.html.twig', [
            'frigaeditalpontuacao' => $pontuacaoCategoria,
            'frigaedital' => $frigaEdital,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return RedirectResponse|Response
     */
    public function formPontuacaoCategoriaAction(Request $request, FrigaEdital $frigaEdital, FrigaEditalPontuacaoCategoria $pontuacaoCategoria = null)
    {
        $this->getUser();
        if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }

        if (!$pontuacaoCategoria) {
            $pontuacaoCategoria = new FrigaEditalPontuacaoCategoria();
            $pontuacaoCategoria->setIdEdital($frigaEdital);
        }

        $form = $this->createForm(FrigaEditalPontuacaoCategoriaType::class, $pontuacaoCategoria);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pontuacaoCategoria);
            $em->flush();

            return $this->redirectToRoute('edital_pontuacao', ['uuid' => $frigaEdital->getUuid()]);
        }

        return $this->render('NteAplicacaoFrigaBundle:edital:form-pontuacao-categoria.html.twig', [
            'frigaeditalpontuacao' => $pontuacaoCategoria,
            'frigaedital' => $frigaEdital,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function removerPontuacaoCategoriaAction(Request $request, FrigaEdital $frigaedital, FrigaEditalPontuacaoCategoria $pontuacaoCategoria)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($pontuacaoCategoria);
            $em->flush();
        } catch (\Exception $e) {
        }

        return $this->redirectToRoute('edital_pontuacao', ['uuid' => $frigaedital->getUuid()]);
    }

    /**
     * @return JsonResponse
     */
    public function apiSituacaoAction(Request $request, FrigaEdital $frigaedital, $situacao)
    {
        $obj = new \stdClass();
        $obj->error = false;
        $obj->msg = 'Edital alterado com sucesso!';

        try {
            $em = $this->getDoctrine()->getManager();
            $frigaedital->setSituacao($situacao);
            $em->persist($frigaedital);
            $em->flush();
        } catch (\Exception $e) {
            $obj->error = true;
            $obj->msg = 'ERRO: ' . $e->getMessage();
        }

        return $this->json($obj);
    }

    /**
     * Creates a new frigaedital entity.
     */
    public function criarAction(Request $request)
    {
        $obj = new \stdClass();
        $obj->error = false;
        $obj->msg = 'Edital criado com sucesso!';
        $obj->uuid = 0;
        try {
            if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
                $obj->error = true;
                $obj->msg = 'Você não possui permissão para criar um novo edital';

                return $this->json($obj);
            }
            $em = $this->getDoctrine()->getManager();
            $frigaedital = new FrigaEdital();

            if ($request->request->get('m0')) {
                $edital = $em->find(FrigaEdital::class, $request->request->get('m0'));
                $frigaedital = $this->copiarConfiguracao($edital, $frigaedital);
            }
            $frigaedital->setUuid(\uniqid());
            $frigaedital->setDataPublicacaoOficial(new \DateTime($request->request->get('data')));
            $frigaedital->setTitulo($request->request->get('titulo'));
            $frigaedital->setUrl($request->request->get('url'));
            $frigaedital->setSituacao(0);

            $em->persist($frigaedital);
            $em->flush();

            if (!$this->isGranted('ROLE_ADMIN')) {
                $eu = new FrigaEditalUsuario();
                $eu->setIdUsuario($this->getUser());
                $eu->setAdministrador(1);
                $eu->setIdEdital($frigaedital);

                $em->persist($eu);
                $em->flush();
            }
            if ($request->request->get('m1')) {
                $edital = $em->find(FrigaEdital::class, $request->request->get('m1'));
                $this->copiarEtapa($edital, $frigaedital);
            }
            if ($request->request->get('m2')) {
                $edital = $em->find(FrigaEdital::class, $request->request->get('m2'));
                $this->copiarPontuacao($edital, $frigaedital);
            }
            $obj->uuid = $frigaedital->getUuid();
        } catch (\Exception $e) {
            $obj->error = true;
            $obj->msg = 'ERRO: ' . $e->getMessage();
        }

        return $this->json($obj);
    }

    /**
     * @return RedirectResponse|Response
     */
    public function importarAction(Request $request, FrigaEdital $frigaEdital)
    {
        if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }

        $em = $this->getDoctrine()->getManager();

        $configForm = $this->createForm(FrigaEditalExportadorType::class);
        $configForm->handleRequest($request);

        if ($configForm->isSubmitted() && $configForm->isValid()) {
        }

        return $this->render('NteAplicacaoFrigaBundle:edital:form-importador.html.twig', [
            'frigaedital' => $frigaEdital,
            'config_form' => $configForm->createView(),
        ]);
    }

    /**
     * @return RedirectResponse|Response
     */
    public function exportarAction(Request $request, FrigaEdital $frigaEdital)
    {
        if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }

        $em = $this->getDoctrine()->getManager();

        $configForm = $this->createForm(FrigaEditalExportadorType::class);
        $configForm->handleRequest($request);
        if ($configForm->isSubmitted() && $configForm->isValid()) {
            $base = '/tmp/friga/';
            if (!\file_exists($base)) {
                \mkdir($base);
            }
            $arquivo = $base . 'exportacao_' . \date('ymd') . '.friga';
            $zip = new \ZipArchive();
            $zip->open($arquivo, \ZipArchive::CREATE);

            // Verifica as configurações do edital
            if ($configForm->get('configuracao')->getData()) {
                $tmp = new \stdClass();
                $tmp->titulo = $frigaEdital->getTitulo();
                $tmp->subtitulo = $frigaEdital->getSubtitulo();
                $tmp->url = $frigaEdital->getUrl();
                $tmp->publico = $frigaEdital->getPublico();

                // $tmp->situacao = $frigaEdital->getSituacao();
                $tmp->sobre = $frigaEdital->getInfo1();
                $tmp->cargos = $frigaEdital->getInfo2();
                $tmp->remuneracao = $frigaEdital->getInfo2();
                $tmp->duvidas = $frigaEdital->getInfo3();
                $tmp->dataPublicacao = $frigaEdital->getDataPublicacaoOficial();

                // Se existir categoria, então exporta ID e Descrição
                $tmp->categoria = new \stdClass();
                if ($frigaEdital->getIdCategoria()) {
                    $tmp->categoria->id = $frigaEdital->getIdCategoria()->getId();
                    $tmp->categoria->descricao = $frigaEdital->getIdCategoria()->getDescricao();
                }
                $zip->addFromString('configuracao.json', \json_encode($tmp));
            }

            //Verifica os arquivos do edital  e adiciona
            if ($configForm->get('arquivo')->getData()) {
                $tmp = new \stdClass();
                $zip->addFromString('arquivo.json', \json_encode($tmp));
            }

            //Verifica os arquivos do edital
            if ($configForm->get('termo')->getData()) {
                $tmp = new \stdClass();
                $zip->addFromString('termo.json', \json_encode($tmp));
            }

            //Verifica o cadastro de vagas
            if ($configForm->get('vaga')->getData()) {
                $tmp = new \stdClass();
                $zip->addFromString('vaga.json', \json_encode($tmp));
            }

            //Verifica o cadastro de listas
            if ($configForm->get('lista')->getData()) {
                $tmp = new \stdClass();
                $zip->addFromString('lista.json', \json_encode($tmp));
            }

            //Verifica as etapas
            if ($configForm->get('etapa')->getData()) {
                $tmp = new \stdClass();
                $zip->addFromString('etapa.json', \json_encode($tmp));
            }

            $zip->close();

            return $this->file($arquivo);
        }

        return $this->render('NteAplicacaoFrigaBundle:edital:form-exportador.html.twig', [
            'frigaedital' => $frigaEdital,
            'config_form' => $configForm->createView(),
        ]);
    }

    /**
     * @return RedirectResponse|Response
     */
    public function editarAction(Request $request, FrigaEdital $frigaEdital)
    {
        if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }

        $em = $this->getDoctrine()->getManager();

        $deleteForm = $this->createDeleteForm($frigaEdital);
        $configForm = $this->createForm(FrigaEditalType::class, $frigaEdital);
        $configForm->handleRequest($request);

        if ($configForm->isSubmitted() && $configForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('info', 'Salvo!');
        }

        return $this->render('NteAplicacaoFrigaBundle:edital:form-config.html.twig', [
            'frigaedital' => $frigaEdital,
            'config_form' => $configForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * @return RedirectResponse|Response
     */
    public function editarTermoAction(Request $request, FrigaEdital $frigaEdital)
    {
        if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }

        $em = $this->getDoctrine()->getManager();

        $deleteForm = $this->createDeleteForm($frigaEdital);
        $configForm = $this->createForm(FrigaEditalTermoType::class, $frigaEdital);
        $configForm->handleRequest($request);

        if ($configForm->isSubmitted() && $configForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('info', 'Salvo!');
        }

        return $this->render('NteAplicacaoFrigaBundle:edital:form-termo.html.twig', [
            'frigaedital' => $frigaEdital,
            'config_form' => $configForm->createView(),
        ]);
    }

    /**
     * @return RedirectResponse|Response
     */
    public function editarResultadoAction(Request $request, FrigaEdital $frigaEdital)
    {
        if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }

        $em = $this->getDoctrine()->getManager();

        $deleteForm = $this->createDeleteForm($frigaEdital);
        $configForm = $this->createForm(FrigaEditalResultadoType::class, $frigaEdital);
        $configForm->handleRequest($request);

        if ($configForm->isSubmitted() && $configForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Salvo!');
        }

        return $this->render('NteAplicacaoFrigaBundle:edital:form-resultado.html.twig', [
            'frigaedital' => $frigaEdital,
            'config_form' => $configForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * @return RedirectResponse|Response
     */
    public function editarInscricoesAction(Request $request, FrigaEdital $frigaEdital)
    {
        if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }

        $em = $this->getDoctrine()->getManager();

        $deleteForm = $this->createDeleteForm($frigaEdital);
        $configForm = $this->createForm(FrigaEditalConfigInscricaoType::class, $frigaEdital);
        $configForm->handleRequest($request);

        if ($configForm->isSubmitted() && $configForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('info', 'Salvo!');
        }

        return $this->render('NteAplicacaoFrigaBundle:edital:form-config-inscricao.html.twig', [
            'frigaedital' => $frigaEdital,
            'config_form' => $configForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Creates a form to delete a frigaedital entity.
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(FrigaEdital $frigaedital)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('edital_remover', ['uuid' => $frigaedital->getUuid()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @return RedirectResponse
     */
    public function clonarEditalAction(Request $request, FrigaEdital $edital)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $editalNovo = new FrigaEdital();

            $editalNovo
                ->setTitulo('Cópia do edital - ' . $edital->getTitulo())
                ->setSubTitulo($edital->getSubtitulo())
                ->setUrl($edital->getUrl())
                ->setIdCategoria($edital->getIdCategoria())
                ->setSobre($edital->getSobre())
                ->setInfo1($edital->getInfo1())
                ->setInfo2($edital->getInfo2())
                ->setInfo3($edital->getInfo3())
                ->setInfo4($edital->getInfo4())
                ->setInfo5($edital->getInfo5())
                ->setInfo6($edital->getInfo6())
                ->setInfo7($edital->getInfo7())
                ->setInfo8($edital->getInfo8())
                ->setInfo9($edital->getInfo9())
                ->setInfo10($edital->getInfo10())
                ->setInfo11($edital->getInfo11())
                ->setInfo12($edital->getInfo12())
                ->setInfo13($edital->getInfo13())
                ->setDataPublicacaoOficial(new \DateTime())
                ->setSituacao(0)
                ->setPublico(0)
                ->setUuid(\uniqid())
                ->setAnexo0($edital->getAnexo0())
                ->setAnexo1($edital->getAnexo1())
                ->setAnexo2($edital->getAnexo2())
                ->setAnexo3($edital->getAnexo3())
                ->setAnexo4($edital->getAnexo4())
                ->setAnexo5($edital->getAnexo5())
                ->setCampoCargoTitulo($edital->getCampoCargoTitulo())
                ->setCampoListaTitulo($edital->getCampoListaTitulo())
                ->setDoc0($edital->getDoc0())
                ->setDoc1($edital->getDoc1())
                ->setDoc2($edital->getDoc2())
                ->setDoc3($edital->getDoc3())
                ->setDoc4($edital->getDoc4())
                ->setDoc5($edital->getDoc5())
                ->setDoc6($edital->getDoc6())
                ->setDoc7($edital->getDoc7())
                ->setDoc8($edital->getDoc8())
                ->setDoc9($edital->getDoc9())
                ->setDoc10($edital->getDoc10())
                ->setDoc11($edital->getDoc11())
                ->setDoc12($edital->getDoc12())
                ->setDoc13($edital->getDoc13())
                ->setDoc14($edital->getDoc14())
                ->setDoc15($edital->getDoc15())
                ->setModeloInscricao($edital->getModeloInscricao())
                ->setTipoInscricao($edital->getTipoInscricao())
                ->setTipoInscricaoLimite($edital->getTipoInscricaoLimite())
                ->setProjetoParticipanteMax($edital->getProjetoParticipanteMax())
                ->setProjetoParticipanteMin($edital->getProjetoParticipanteMin())
                ->setPermitirEstrangeiro($edital->getPermitirEstrangeiro())
                ->setTermoCompromisso($edital->getTermoCompromisso())
                ->setTermoCompromissoSituacao($edital->getTermoCompromissoSituacao())
                ->setResultado0($edital->isResultado0())
                ->setResultado1($edital->isResultado1())
                ->setResultado2($edital->isResultado2())
                ->setResultado3($edital->isResultado3());
            $em->persist($editalNovo);
            $em->flush();

            $editalNovo = $em->find(FrigaEdital::class, $editalNovo->getId());
            if (\is_null($editalNovo)) {
                $this->addFlash('danger', 'Erro ao clonar edital');

                return $this->redirectToRoute('edital_index_rascunho');
            }

            /** @var FrigaEditalCargo $cargo */
            foreach ($edital->getCargo() as $cargo) {
                $cargoNovo = clone $cargo;
                $cargoNovo->setIdEdital($editalNovo);
                $em->persist($cargoNovo);
                $em->flush();
            }

            /** @var FrigaEditalCota $cota */
            foreach ($edital->getCota() as $cota) {
                $cotaNova = clone $cota;
                $cotaNova->setIdEdital($editalNovo);
                $em->persist($cotaNova);
                $em->flush();
            }

            $tmpEtapa = [];
            /** @var FrigaEditalEtapa $etapa */
            foreach ($edital->getEtapa() as $etapa) {
                if (\key_exists($etapa->getId(), $tmpEtapa)) {
                    continue;
                }
                $etapaNova = clone $etapa;
                $etapaNova->setIdEdital($editalNovo);
                $em->persist($etapaNova);
                $em->flush();
                $tmpEtapa[$etapa->getId()] = $etapaNova;

                if (!\is_null($etapa->getIdEtapa())) {
                    if (!\key_exists($etapa->getIdEtapa()->getId(), $tmpEtapa)) {
                        $ccc = clone $etapa->getIdEtapa();
                        $ccc->setIdEdital($editalNovo);
                        $em->persist($ccc);
                        $em->flush();
                        $tmpEtapa[$etapa->getIdEtapa()->getId()] = $ccc;
                    }
                    $tmpEtapa[$etapa->getId()]->setIdEtapa($tmpEtapa[$etapa->getIdEtapa()->getId()]);
                    $em->persist($tmpEtapa[$etapa->getId()]);
                    $em->flush();
                }
            }

            $tmpCategoria = [];
            /** @var FrigaEditalPontuacaoCategoria $categoria */
            foreach ($edital->getPontuacaoCategoria() as $categoria) {
                if (\key_exists($categoria->getId(), $tmpCategoria)) {
                    continue;
                }
                $cc = clone $categoria;
                $cc->setIdEdital($editalNovo);
                $em->persist($cc);
                $em->flush();
                $tmpCategoria[$categoria->getId()] = $cc;

                if (!\is_null($categoria->getIdCategoria())) {
                    if (!\key_exists($categoria->getIdCategoria()->getId(), $tmpCategoria)) {
                        $ccc = clone $categoria->getIdCategoria();
                        $ccc->setIdEdital($editalNovo);
                        $em->persist($ccc);
                        $em->flush();
                        $tmpCategoria[$categoria->getIdCategoria()->getId()] = $ccc;
                    }
                    $tmpCategoria[$categoria->getId()]->setIdCategoria($tmpCategoria[$categoria->getIdCategoria()->getId()]);
                    $em->persist($tmpCategoria[$categoria->getId()]);
                    $em->flush();
                }
            }

            /** @var FrigaEditalPontuacao $pt */
            foreach ($edital->getPontuacao() as $pt) {
                $cc = $pt->getIdCategoria() ? $tmpCategoria[$pt->getIdCategoria()->getId()] : null;
                $pontuacao = clone $pt;
                $pontuacao
                    ->setIdEdital($editalNovo)
                    ->setIdCategoria($cc);

                if (!$pt->getIdEtapa()->isEmpty()) {
                    foreach ($pt->getIdEtapa() as $etapa) {
                        if (\array_key_exists($etapa->getId(), $tmpEtapa)) {
                            $pontuacao->addIdEtapa($tmpEtapa[$etapa->getId()]);
                        } else {
                            dump($etapa);
                            dump($etapa->getIdEdital()->getId());
                            dump($editalNovo->getId());
                        }
                    }
                }
                $em->persist($pontuacao);
                $em->flush();
            }
            $this->addFlash('success', 'O edital foi copiado com sucesso!');
        } catch (\Exception $e) {
            $this->addFlash('danger', $e->getMessage());
            $this->addFlash('danger', $e->getLine());
        }

        return $this->redirectToRoute('edital_index_rascunho');
    }

    /**
     * @return RedirectResponse
     */
    public function clonarEtapaAction(Request $request, FrigaEdital $destino, FrigaEdital $origem)
    {
        if (!\is_null($origem)
            and !\is_null($destino)
            and $origem->getId() != $destino->getId()) {
            try {
                $this->copiarEtapa($origem, $destino);
                $this->addFlash('success', 'As etapas foram copiadas com sucesso!');
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage());
                $this->addFlash('danger', $e->getLine());
            }
        }

        return $this->redirectToRoute('edital_etapa', ['uuid' => $destino->getUuid()]);
    }

    /**
     * @return RedirectResponse
     */
    public function clonarPontuacaoAction(Request $request, FrigaEdital $destino, FrigaEdital $origem)
    {
        try {
            $this->copiarPontuacao($origem, $destino);
            $this->addFlash('success', 'As etapas foram copiadas com sucesso!');
        } catch (\Exception $e) {
            $this->addFlash('danger', $e->getMessage());
            $this->addFlash('danger', $e->getLine());
        }

        return $this->redirectToRoute('edital_pontuacao', ['uuid' => $destino->getUuid()]);
    }

    /**
     * Remove um edital.
     *
     * @return JsonResponse
     */
    public function removerEditalAction(Request $request, FrigaEdital $edital)
    {
        $obj = new \stdClass();
        $obj->error = false;
        $obj->msg = 'Edital removido com sucesso!';

        $em = $this->getDoctrine()->getManager();
        if (0 != $edital->getSituacao() or $edital->getInscricaoValida()->count()) {
            $obj->error = true;
            $obj->msg = 'Impossível remover o edital!';

            return $this->json($obj);
        }
        try {
            foreach ($edital->getInscricao() as $item) {
                $em->remove($item);
                $em->flush();
            }
            $em->remove($edital);
            $em->flush();
        } catch (\Exception $e) {
            $obj->error = true;
            $obj->msg = 'ERRO:' . $e->getMessage();
        }

        return $this->json($obj);
    }

    public function copiarConfiguracao(FrigaEdital $edital, FrigaEdital $destino)
    {
        $destino
            ->setTitulo('Cópia do edital - ' . $edital->getTitulo())
            ->setSubTitulo($edital->getSubtitulo())
            ->setUrl($edital->getUrl())
            ->setIdCategoria($edital->getIdCategoria())
            ->setSobre($edital->getSobre())
            ->setInfo1($edital->getInfo1())
            ->setInfo2($edital->getInfo2())
            ->setInfo3($edital->getInfo3())
            ->setInfo4($edital->getInfo4())
            ->setInfo5($edital->getInfo5())
            ->setInfo6($edital->getInfo6())
            ->setInfo7($edital->getInfo7())
            ->setInfo8($edital->getInfo8())
            ->setInfo9($edital->getInfo9())
            ->setInfo10($edital->getInfo10())
            ->setInfo11($edital->getInfo11())
            ->setInfo12($edital->getInfo12())
            ->setInfo13($edital->getInfo13())
            ->setDataPublicacaoOficial(new \DateTime())
            ->setSituacao(0)
            ->setPublico(0)
            ->setUuid(\uniqid())
            ->setAnexo0($edital->getAnexo0())
            ->setAnexo1($edital->getAnexo1())
            ->setAnexo2($edital->getAnexo2())
            ->setAnexo3($edital->getAnexo3())
            ->setAnexo4($edital->getAnexo4())
            ->setAnexo5($edital->getAnexo5())
            ->setCampoCargoTitulo($edital->getCampoCargoTitulo())
            ->setCampoListaTitulo($edital->getCampoListaTitulo())
            ->setDoc0($edital->getDoc0())
            ->setDoc1($edital->getDoc1())
            ->setDoc2($edital->getDoc2())
            ->setDoc3($edital->getDoc3())
            ->setDoc4($edital->getDoc4())
            ->setDoc5($edital->getDoc5())
            ->setDoc6($edital->getDoc6())
            ->setDoc7($edital->getDoc7())
            ->setDoc8($edital->getDoc8())
            ->setDoc9($edital->getDoc9())
            ->setDoc10($edital->getDoc10())
            ->setDoc11($edital->getDoc11())
            ->setDoc12($edital->getDoc12())
            ->setDoc13($edital->getDoc13())
            ->setDoc14($edital->getDoc14())
            ->setDoc15($edital->getDoc15())
            ->setModeloInscricao($edital->getModeloInscricao())
            ->setTipoInscricao($edital->getTipoInscricao())
            ->setTipoInscricaoLimite($edital->getTipoInscricaoLimite())
            ->setProjetoParticipanteMax($edital->getProjetoParticipanteMax())
            ->setProjetoParticipanteMin($edital->getProjetoParticipanteMin())
            ->setPermitirEstrangeiro($edital->getPermitirEstrangeiro())
            ->setTermoCompromisso($edital->getTermoCompromisso())
            ->setTermoCompromissoSituacao($edital->getTermoCompromissoSituacao())
            ->setResultado0($edital->isResultado0())
            ->setResultado1($edital->isResultado1())
            ->setResultado2($edital->isResultado2())
            ->setResultado3($edital->isResultado3());

        return $destino;
    }

    /**
     * @return array
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function copiarEtapa(FrigaEdital $origem, FrigaEdital $destino)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $tmpEtapa = [];

        /** @var FrigaEditalEtapa $etapa */
        foreach ($origem->getEtapaCronologica() as $etapa) {
            if (\key_exists($etapa->getId(), $tmpEtapa)) {
                continue;
            }
            $etapaNova = clone $etapa;
            $etapaNova->setIdEdital($destino);
            $em->persist($etapaNova);
            $em->flush();

            $etapaNova->setPR($etapa->getPR(), $destino->getDataPublicacaoOficial());
            $em->persist($etapaNova);
            $em->flush();
            $tmpEtapa[$etapa->getId()] = $etapaNova;

            if (!\is_null($etapa->getIdEtapa())) {
                if (!\key_exists($etapa->getIdEtapa()->getId(), $tmpEtapa)) {
                    $ccc = clone $etapa->getIdEtapa();
                    $ccc->setIdEdital($destino);
                    $em->persist($ccc);
                    $em->flush();

                    $ccc->setPR($etapa->getPR(), $destino->getDataPublicacaoOficial());
                    $em->persist($ccc);
                    $em->flush();

                    $tmpEtapa[$etapa->getIdEtapa()->getId()] = $ccc;
                }
                $tmpEtapa[$etapa->getId()]->setIdEtapa($tmpEtapa[$etapa->getIdEtapa()->getId()]);
                $em->persist($tmpEtapa[$etapa->getId()]);
                $em->flush();
            }
        }

        return $tmpEtapa;
    }

    /**
     * @param array $etapas
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function copiarPontuacao(FrigaEdital $origem, FrigaEdital $destino, $etapas = [])
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $tmpCategoria = [];

        /** @var FrigaEditalPontuacaoCategoria $categoria */
        foreach ($origem->getPontuacaoCategoria() as $categoria) {
            if (\key_exists($categoria->getId(), $tmpCategoria)) {
                continue;
            }
            $cc = clone $categoria;
            $cc->setIdEdital($destino);
            $em->persist($cc);
            $em->flush();
            $tmpCategoria[$categoria->getId()] = $cc;

            if (!\is_null($categoria->getIdCategoria())) {
                if (!\key_exists($categoria->getIdCategoria()->getId(), $tmpCategoria)) {
                    $ccc = clone $categoria->getIdCategoria();
                    $ccc->setIdEdital($destino);
                    $em->persist($ccc);
                    $em->flush();
                    $tmpCategoria[$categoria->getIdCategoria()->getId()] = $ccc;
                }
                $tmpCategoria[$categoria->getId()]->setIdCategoria($tmpCategoria[$categoria->getIdCategoria()->getId()]);
                $em->persist($tmpCategoria[$categoria->getId()]);
                $em->flush();
            }
        }

        /** @var FrigaEditalPontuacao $pt */
        foreach ($origem->getPontuacao() as $pt) {
            $cc = $pt->getIdCategoria() ? $tmpCategoria[$pt->getIdCategoria()->getId()] : null;
            $pontuacao = clone $pt;
            $pontuacao->setIdEdital($destino)->setIdCategoria($cc);

            if (!$pt->getIdEtapa()->isEmpty()) {
                foreach ($pt->getIdEtapa() as $etapa) {
                    if (!\array_key_exists($etapa->getId(), $etapa)) {
                        continue;
                    }
                    $pontuacao->addIdEtapa($etapas[$etapa->getId()]);
                }
            }
            $em->persist($pontuacao);
            $em->flush();
        }
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

    public function indexLogsAction(Request $request, FrigaEdital $edital)
    {
        $logs = $this->getLog($edital);

        return $this->render('NteAplicacaoFrigaBundle:edital:index-logs.html.twig', [
            'frigaedital' => $edital,
            'logs' => $logs,
        ]);
    }

    /**
     * @return BinaryFileResponse
     */
    public function logsExportarCsvAction(Request $request, FrigaEdital $edital)
    {
        $cabecalho = [
            0 => 'ID',
            1 => 'DATA',
            2 => 'USUARIO',
            3 => 'USUARIO_CPF',
            4 => 'OPERACAO',
            5 => 'METODO',
            6 => 'URI',
            7 => 'CONTEXTO_PRIMARIO',
            8 => 'CONTEXTO_SECUNDARIO',
            9 => 'MSG',
            10 => 'USUARIO_AFETADO_NOME',
            11 => 'USUARIO_AFETADO_CPF',
        ];
        $arquivo = '/tmp/logs-edital-' . $edital->getUuid() . '.csv';
        if (\is_file($arquivo)) {
            \unlink($arquivo);
        }
        $out = \fopen($arquivo, 'w');
        \fputcsv($out, $cabecalho);
        foreach ($this->getLog($edital) as $log) {
            $linha = [
                0 => $log->id,
                1 => $log->data->format('Y-m-d H:i:s'),
                2 => \strtoupper($log->idUsuario->getNome()),
                3 => $log->idUsuario->getCpf(),
                4 => $log->operacao,
                5 => $log->item->getMetodo(),
                6 => $log->item->getUri(),
                7 => $log->contexto,
                8 => $log->contextoSecundario,
                9 => $log->msg,
                10 => \is_null($log->idUsuario2) ? '' : \strtoupper($log->idUsuario2->getNome()),
                11 => \is_null($log->idUsuario2) ? '' : $log->idUsuario2->getCpf(),
            ];
            \fputcsv($out, $linha);
        }
        \fclose($out);

        return $this->file($arquivo);
    }

    public function getLog(FrigaEdital $edital)
    {
        $inscricoes = [];
        $recursos = [];
        $etapas = [];
        $arquivos = [];
        $convocacoes = [];
        $pontuacoes = [];
        $categorias = [];
        $convites = [];

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $urlConfiguracaoEdital = [
            '/app/edital/remover/{uuid}', '/app/edital/{uuid}/exportador', '/app/edital/{uuid}/exportar/csv/logs', '/app/edital/{uuid}/importar', '/app/edital/{uuid}/config', '/app/edital/{uuid}/termo', '/app/edital/{uuid}/inscricoes', '/app/edital/{uuid}/resultados', '/app/edital/{uuid}/arquivo', '/app/edital/{uuid}/avaliador', '/app/edital/{uuid}/api/avaliador/lista', '/app/edital/{uuid}/avaliador/criar', '/app/edital/{uuid}/avaliador/convidar', '/app/edital/{uuid}/avaliador/convidar/{convite}', '/app/edital/{uuid}/avaliador/convidar/remover/{convite}', '/app/edital/{uuid}/avaliador/convidar/enviar/{convite}', '/app/edital/{uuid}/avaliador/editar/{avaliador}', '/app/edital/{uuid}/avaliador/remover/{avaliador}', '/app/edital/{uuid}/avaliador/adicionar/{usuario}/cargo', '/app/edital/{uuid}/etapa', '/app/edital/{uuid}/etapa/novo/{tipo}', '/app/edital/{uuid}/etapa/{etapa}/editar', '/app/edital/{uuid}/etapa/{etapa}/remover', '/app/edital/{uuid}/etapa-categoria/criar', '/app/edital/{uuid}/etapa-categiria/{categoria}/editar', '/app/edital/{uuid}/etapa-categoria/{categoria}/remover', '/app/edital/{uuid}/pontuacao', '/app/edital/{uuid}/pontuacao/novo', '/app/edital/{uuid}/pontuacao/{pontuacao}/editar', '/app/edital/{uuid}/pontuacao/{pontuacao}/remover', '/app/edital/{uuid}/pontuacao-categoria/novo', '/app/edital/{uuid}/pontuacao-categoria/{pontuacaoCategoria}/editar', '/app/edital/{uuid}/pontuacao-categoria/{pontuacaoCategoria}/remover', '/app/edital/{uuid}/pontuacao-categoria-peso/novo', '/app/edital/{uuid}/pontuacao-categoria-peso/{pontuacaoCategoria}/editar', '/app/edital/{uuid}/cargo/', '/app/edital/{uuid}/cargo/novo', '/app/edital/{uuid}/cargo/{cargo}/editar', '/app/edital/{uuid}/cargo/{cargo}/remover', '/app/edital/{uuid}/cota/', '/app/edital/{uuid}/cota/novo', '/app/edital/{uuid}/cota/{cota}/editar', '/app/edital/{uuid}/cota/{cota}/remover', '/app/edital/{uuid}/desempate/', '/app/edital/{uuid}/desempate/ordem/{criterio}/{tipo}', '/app/edital/{uuid}/desempate/novo/{tipo}', '/app/edital/{uuid}/desempate/{criterio}/editar', '/app/edital/{uuid}/desempate/{criterio}/remover', '/app/edital/{uuid}/logs',
        ];
        $urlAvalicao = [
            '/app/avaliacao/etapa/{etapa}', '/app/avaliacao/etapa/{etapa}/exportar/csv', '/app/resultado/parcial/{etapa}', '/app/resultado/etapa/{etapa}/', '/app/resultado/etapa/{etapa}/confirmar-posicao', '/app/resultado/etapa/{etapa}/remover', '/app/resultado/etapa/{etapa}/gerar', '/app/resultado/etapa/{etapa}/form', '/app/resultado/etapa/{etapa}/impressao', '/app/resultado/etapa/{etapa}/exportar/csv', '/app/convocacao/etapa/{etapa}', '/app/convocacao/etapa/{etapa}/impressao/relacao', '/app/convocacao/etapa/{etapa}/impressao/relacao-contato', '/app/convocacao/etapa/{etapa}/impressao/presenca', '/app/convocacao/etapa/{etapa}/exportar/csv', '/app/convocacao/etapa/{etapa}/exportar/moodle',
        ];
        $urlAvalicaoCandidato = [
            '/app/avaliacao/etapa/{etapa}/inscricao/{uuid}', '/app/convocacao/etapa/{etapa}/inscricao/{uuid}', '/app/relatorio/inscricao-perfil/{uuid}',
        ];
        $urlArquivo = [
            '/app/arquivo/visualizar/{id}',
        ];
        $urlResultadoAjuste = [
            '/app/resultado/etapa/{etapa}/posicao/{uuid}',
        ];
        $urlCandidato = [
            '/candidato/cancelar/{uuid}', '/candidato/inscricacao-concluida/{uuid}', '/candidato/inscricacao-realizada/{uuid}', '/candidato/inscricao-projeto/{uuid}',
        ];
        $urlCandidatoEtapa = [
            '/candidato/recursos/{etapa}/formulario/{uuid}',
        ];
        $param = [
            '/candidato/inscricacao/' . $edital->getUuid(), '/app/relatorio/resumo/' . $edital->getUuid(), '/app/relatorio/andamento/' . $edital->getUuid(), '/app/relatorio/convocacao/' . $edital->getUuid(), '/app/relatorio/recurso/' . $edital->getUuid(), '/app/relatorio/inscritos/' . $edital->getUuid(),
        ];
        /** @var FrigaInscricao $item */
        foreach ($edital->getInscricao() as $item) {
            $inscricoes[$item->getUuid()] = $item;
            /** @var FrigaArquivo $subitem */
            foreach ($item->getIdArquivo() as $subitem) {
                $arquivos[$subitem->getId()] = $subitem;
            }
            /** @var FrigaInscricaoPontuacao $subitem */
            foreach ($item->getPontuacao() as $subitem) {
                /** @var FrigaArquivo $subsubitem */
                foreach ($subitem->getIdArquivo() as $subsubitem) {
                    $arquivos[$subsubitem->getId()] = $subsubitem;
                }
                $pt = $subitem->getIdEditalPontuacao();
                $cat = $pt->getIdCategoria();
                $categorias[$cat->getId()] = $cat;
                $pontuacoes[$pt->getId()] = $pt;
            }
        }

        /** @var FrigaEditalEtapa $item */
        foreach ($edital->getEtapa() as $item) {
            $etapas[$item->getId()] = $item;

            /** @var FrigaConvocacao $subitem */
            foreach ($item->getConvocacao() as $subitem) {
                $convocacoes[$subitem->getId()] = $subitem;
            }
        }

        /** @var FrigaInscricaoRecurso $item */
        foreach ($edital->getRecursos() as $item) {
            $recursos[$item->getUuid()] = $item;
            $param[] = \str_replace('{uuid}', $item->getUuid(), '/candidato/recursos/{uuid}/ver/');
        }
        /** @var FrigaEditalUsuarioConvite $item */
        foreach ($edital->getIdEditalUsuarioConvite() as $item) {
            $convite[$item->getId()] = $item;
        }

        foreach ($urlConfiguracaoEdital as $url) {
            $param[] = \str_replace('{uuid}', $edital->getUuid(), $url);
        }

        /**
         * @var $id
         * @var $arquivo
         */
        foreach ($arquivos as $id => $arquivo) {
            foreach ($urlArquivo as $url) {
                $param[] = \str_replace('{id}', $id, $url);
            }
        }
        foreach ($etapas as $id => $etapa) {
            foreach ($urlAvalicao as $url) {
                $param[] = \str_replace('{etapa}', $id, $url);
            }
            foreach ($inscricoes as $uiid => $inscricao) {
                foreach ($urlAvalicaoCandidato as $url) {
                    $url = \str_replace('{etapa}', $id, $url);
                    $url = \str_replace('{uuid}', $uiid, $url);
                    $param[] = $url;
                }
                foreach ($urlCandidato as $url) {
                    $url = \str_replace('{uuid}', $uiid, $url);
                    $param[] = $url;
                }
                foreach ($urlCandidatoEtapa as $url) {
                    $url = \str_replace('{etapa}', $id, $url);
                    $url = \str_replace('{uuid}', $uiid, $url);
                    $param[] = $url;
                }
            }
        }

        $aux = new \stdClass();
        $aux->etapa = $etapas;
        $aux->inscricao = $inscricoes;
        $aux->recurso = $recursos;
        $aux->arquivo = $arquivos;
        $aux->pontuacao = $pontuacoes;
        $aux->categoria = $categorias;
        $aux->convocacao = $convocacoes;
        $aux->convite = $convites;
        $aux->edital = $edital;

        $logs = $em->createQueryBuilder()
            ->select('l')
            ->from(Log::class, 'l')
            ->Where('l.uri in (:uri)')
            ->setParameter('uri', $param)
            ->getQuery()
            ->getResult();

        $tmp = [];

        /** @var Log $log */
        foreach ($logs as $log) {
            $ctx = $log->getCtxAux($aux);

            $obj = new \stdClass();
            $obj->id = $log->getId();
            $obj->data = $log->getRegistroDataCriacao();
            $obj->idUsuario = $log->getIdUsuario();
            $obj->idUsuario2 = $ctx->usuario;
            $obj->msg = $ctx->msg;
            $obj->msg = $ctx->msg;
            $obj->contexto = $ctx->contexto;
            $obj->usuario = \is_null($ctx->usuario) ? '-' : $ctx->usuario->getNome();
            $obj->operacao = $ctx->operacao;
            $obj->item = $log;
            $tmp[] = $obj;
        }

        return $tmp;
    }
}
