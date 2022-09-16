<?php

namespace Nte\Aplicacao\FrigaBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCargo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCategoria;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCota;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacaoCategoria;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao;
use Nte\Aplicacao\FrigaBundle\Form\FrigaAvaliadorCargoType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaAvaliadorEdicaoType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaAvaliadorType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalCargoType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalCategoriaType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalConfigInscricaoType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalDesempateType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalEtapaType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalCotaType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalExportadorType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalPontuacaoCategoriaPesoType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalPontuacaoCategoriaType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalPontuacaoType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalResultadoType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalTermoType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalType;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalDesempate;
use Nte\UsuarioBundle\Entity\Usuario;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations\Items;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DateTime;
use Exception;
use ZipArchive;

/**
 * frigaedital controller.
 *
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

        return $this->render('NteAplicacaoFrigaBundle:edital:index.html.twig', array(
            'editais' => $frigaEditais,
        ));
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

        return $this->render('NteAplicacaoFrigaBundle:edital:index.html.twig', array(
            'editais' => $frigaEditais,
        ));
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

        return $this->render('NteAplicacaoFrigaBundle:edital:index.html.twig', array(
            'editais' => $frigaEditais,
        ));
    }

    /**
     * @param Request $request
     * @param FrigaEdital $frigaEdital
     * @return Response
     */
    public function indexArquivoAction(Request $request, FrigaEdital $frigaEdital)
    {
        return $this->render('NteAplicacaoFrigaBundle:edital:form-arquivo.html.twig', array(
            'frigaedital' => $frigaEdital,
        ));
    }

    /**
     * @param Request $request
     * @param FrigaEdital $frigaEdital
     * @return Response
     */
    public function indexCargoAction(Request $request, FrigaEdital $frigaEdital)
    {
        return $this->render('NteAplicacaoFrigaBundle:edital:index-cargo.html.twig', array(
            'frigaedital' => $frigaEdital,
        ));
    }

    /**
     * @return Response
     */
    public function indexCategoriaAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categoria = $em->getRepository(FrigaEditalCategoria::class)->findAll();
        return $this->render('NteAplicacaoFrigaBundle:edital:index-categoria.html.twig', array(
            'categoria' => $categoria,
        ));
    }

    /**
     * @param Request $request
     * @param FrigaEditalCategoria $categoria
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
        return $this->render('NteAplicacaoFrigaBundle:edital:form-categoria.html.twig', array(
            'categoria' => $categoria,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @param FrigaEditalCategoria $categoria
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
     * @param Request $request
     * @param FrigaEdital $frigaEdital
     * @param FrigaEditalCargo|null $cargo
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
        return $this->render('NteAplicacaoFrigaBundle:edital:form-cargo.html.twig', array(
            'frigaeditalcargo' => $cargo,
            'frigaedital' => $frigaEdital,
            'form' => $form->createView(),
        ));
    }


    /**
     * @param Request $request
     * @param FrigaEdital $frigaedital
     * @param FrigaEditalCargo $cargo
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
     * @param Request $request
     * @param FrigaEdital $frigaEdital
     * @return Response
     */
    public function indexCotaAction(Request $request, FrigaEdital $frigaEdital)
    {
        return $this->render('NteAplicacaoFrigaBundle:edital:index-cota.html.twig', array(
            'frigaedital' => $frigaEdital,
        ));
    }


    /**
     * @param Request $request
     * @param FrigaEdital $frigaEdital
     * @param FrigaEditalCota|null $cota
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
        return $this->render('NteAplicacaoFrigaBundle:edital:form-cota.html.twig', array(
            'frigaeditalcota' => $cota,
            'frigaedital' => $frigaEdital,
            'form' => $form->createView(),
        ));
    }


    /**
     * @param Request $request
     * @param FrigaEdital $frigaedital
     * @param FrigaEditalCota $cota
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
     * @param Request $request
     * @param FrigaEdital $frigaEdital
     * @return Response
     */
    public function indexAvaliadorAction(Request $request, FrigaEdital $frigaEdital)
    {
        return $this->render('NteAplicacaoFrigaBundle:edital:index-avaliador.html.twig', array(
            'frigaedital' => $frigaEdital,
        ));
    }

    /**
     * @param Request $request
     * @param FrigaEdital $frigaEdital
     * @param FrigaEditalUsuario $avaliador
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
            $configForm = $this->createForm(FrigaAvaliadorType::class, $avaliador);
        } else {
            $configForm = $this->createForm(FrigaAvaliadorEdicaoType::class, $avaliador);
        }

        $configForm->handleRequest($request);

        if ($configForm->isSubmitted() && $configForm->isValid()) {
            $em->persist($avaliador);
            $em->flush();
            $this->addFlash('info', 'Os avalidores foram adicionados com sucesso ao edital');
            return $this->redirectToRoute('edital_avaliador', ['uuid' => $frigaEdital->getUuid()]);
        }

        return $this->render('NteAplicacaoFrigaBundle:edital:form-avaliador.html.twig', array(
            'frigaedital' => $frigaEdital,
            'frigaeditalusuario' => $avaliador,
            'form' => $configForm->createView(),
        ));
    }


    /**
     * @param Request $request
     * @param FrigaEdital $frigaedital
     * @param FrigaEditalUsuario $avaliador
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
     * @param Request $request
     * @param FrigaEdital $frigaEdital
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

        return $this->render('NteAplicacaoFrigaBundle:edital:form-avaliador-cargo.html.twig', array(
            'frigaedital' => $frigaEdital,
            'config_form' => $configForm->createView(),
        ));
    }

    /**
     * @param Request $request
     * @param FrigaEdital $frigaEdital
     * @return Response
     */
    public function indexDesempateAction(Request $request, FrigaEdital $frigaEdital)
    {
        return $this->render('NteAplicacaoFrigaBundle:edital:index-desempate.html.twig', array(
            'frigaedital' => $frigaEdital,
        ));
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
            if (array_key_exists($criterio->getPosicao() + 1, $tmp)) {
                $x = $tmp[$criterio->getPosicao() + 1];
                $tmp[$criterio->getPosicao() + 1] = $tmp[$criterio->getPosicao()];
                $tmp[$criterio->getPosicao()] = $x;
            }

        } else {
            if (array_key_exists($criterio->getPosicao() - 1, $tmp)) {
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
     * @param Request $request
     * @param FrigaEdital $frigaEdital
     * @param int $tipo
     * @return RedirectResponse
     * @throws Exception
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
        }
        $em->persist($entidade);
        $em->flush();

        return $this->redirectToRoute('edital_desempate_editar', ['uuid' => $frigaEdital->getUuid(), 'criterio' => $entidade->getId()]);

    }

    /**
     * @param Request $request
     * @param FrigaEdital $frigaEdital
     * @param FrigaEditalDesempate|null $criterio
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


        return $this->render('NteAplicacaoFrigaBundle:edital:form-desempate.html.twig', array(
            'frigaeditaledesempate' => $criterio,
            'frigaedital' => $frigaEdital,
            'form' => $form->createView(),
        ));
    }


    /**
     * @param Request $request
     * @param FrigaEdital $frigaedital
     * @param FrigaEditalDesempate $criterio
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
     * @param Request $request
     * @param FrigaEdital $frigaEdital
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
            ->getQuery()->getResult();;
        $editais = array_map(function ($e) {
            return (object)$e;
        }, $editais);

        return $this->render('NteAplicacaoFrigaBundle:edital:index-etapa.html.twig', array(
            'frigaedital' => $frigaEdital,
            'editais' => $editais
        ));
    }


    /**
     * @param Request $request
     * @param FrigaEdital $frigaEdital
     * @param int $tipo
     * @return RedirectResponse
     * @throws Exception
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
     * @param Request $request
     * @param FrigaEdital $frigaEdital
     * @param FrigaEditalEtapa|null $entidade
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


        return $this->render('NteAplicacaoFrigaBundle:edital:form-etapa.html.twig', array(
            'frigaeditaletapa' => $etapa,
            'frigaedital' => $frigaEdital,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @param FrigaEdital $frigaedital
     * @param FrigaEditalEtapa $etapa
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
     * @param Request $request
     * @param FrigaEdital $frigaEdital
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
            ->getQuery()->getResult();;
        $editais = array_map(function ($e) {
            return (object)$e;
        }, $editais);

        return $this->render('NteAplicacaoFrigaBundle:edital:index-pontuacao.html.twig', array(
            'frigaedital' => $frigaEdital,
            'editais'=>$editais
        ));
    }

    /**
     *
     * @param Request $request
     * @param FrigaEdital $frigaEdital
     * @param FrigaEditalPontuacao|null $pontuacao
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
        return $this->render('NteAplicacaoFrigaBundle:edital:form-pontuacao.html.twig', array(
            'frigaeditalpontuacao' => $pontuacao,
            'frigaedital' => $frigaEdital,
            'form' => $form->createView(),
        ));
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
     * @param Request $request
     * @param FrigaEdital $frigaEdital
     * @param FrigaEditalPontuacaoCategoria|null $pontuacaoCategoria
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
        return $this->render('NteAplicacaoFrigaBundle:edital:form-pontuacao-categoria.html.twig', array(
            'frigaeditalpontuacao' => $pontuacaoCategoria,
            'frigaedital' => $frigaEdital,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @param FrigaEdital $frigaEdital
     * @param FrigaEditalPontuacaoCategoria|null $pontuacaoCategoria
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
        return $this->render('NteAplicacaoFrigaBundle:edital:form-pontuacao-categoria.html.twig', array(
            'frigaeditalpontuacao' => $pontuacaoCategoria,
            'frigaedital' => $frigaEdital,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @param FrigaEdital $frigaedital
     * @param FrigaEditalPontuacaoCategoria $pontuacaoCategoria
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
     * Creates a new frigaedital entity.
     */
    public function criarAction(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }
        $frigaedital = new FrigaEdital();
        $frigaedital->setUuid(uniqid());
        $frigaedital->setDataPublicacaoOficial(new \DateTime());
        $frigaedital->setTitulo("Novo Edital");
        $frigaedital->setSituacao(0);
        $em = $this->getDoctrine()->getManager();
        $em->persist($frigaedital);
        $em->flush();

        // Configura a categoria de pontuação pai
        // $pontuacaoCategoria = new FrigaEditalPontuacaoCategoria();
        //$pontuacaoCategoria->setIdEdital($frigaedital)->setValor(100.00)->setDescricao("Geral");
        //$em->persist($pontuacaoCategoria);
        //$em->flush();

        // Configura

        return $this->redirectToRoute('edital_index_rascunho');
    }

    /**
     * @param Request $request
     * @param FrigaEdital $frigaEdital
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

        return $this->render('NteAplicacaoFrigaBundle:edital:form-importador.html.twig', array(
            'frigaedital' => $frigaEdital,
            'config_form' => $configForm->createView(),
        ));
    }

    /**
     * @param Request $request
     * @param FrigaEdital $frigaEdital
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

            $base = "/tmp/friga/";
            if (!file_exists($base)) {
                mkdir($base);
            }
            $arquivo = $base . "exportacao_" . date('ymd') . ".friga";
            $zip = new \ZipArchive();
            $zip->open($arquivo, ZipArchive::CREATE);

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
                $zip->addFromString('configuracao.json', json_encode($tmp));
            }

            //Verifica os arquivos do edital  e adiciona
            if ($configForm->get('arquivo')->getData()) {
                $tmp = new \stdClass();
                $zip->addFromString('arquivo.json', json_encode($tmp));
            }

            //Verifica os arquivos do edital
            if ($configForm->get('termo')->getData()) {
                $tmp = new \stdClass();
                $zip->addFromString('termo.json', json_encode($tmp));
            }

            //Verifica o cadastro de vagas
            if ($configForm->get('vaga')->getData()) {
                $tmp = new \stdClass();
                $zip->addFromString('vaga.json', json_encode($tmp));
            }

            //Verifica o cadastro de listas
            if ($configForm->get('lista')->getData()) {
                $tmp = new \stdClass();
                $zip->addFromString('lista.json', json_encode($tmp));
            }

            //Verifica as etapas
            if ($configForm->get('etapa')->getData()) {
                $tmp = new \stdClass();
                $zip->addFromString('etapa.json', json_encode($tmp));
            }


            $zip->close();

            return $this->file($arquivo);


        }

        return $this->render('NteAplicacaoFrigaBundle:edital:form-exportador.html.twig', array(
            'frigaedital' => $frigaEdital,
            'config_form' => $configForm->createView(),

        ));
    }


    /**
     * @param Request $request
     * @param FrigaEdital $frigaEdital
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
            $this->addFlash('info', "Salvo!");
        }

        return $this->render('NteAplicacaoFrigaBundle:edital:form-config.html.twig', array(
            'frigaedital' => $frigaEdital,
            'config_form' => $configForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @param Request $request
     * @param FrigaEdital $frigaEdital
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
            $this->addFlash('info', "Salvo!");
        }

        return $this->render('NteAplicacaoFrigaBundle:edital:form-termo.html.twig', array(
            'frigaedital' => $frigaEdital,
            'config_form' => $configForm->createView(),
        ));
    }

    /**
     * @param Request $request
     * @param FrigaEdital $frigaEdital
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
            $this->addFlash('success', "Salvo!");
        }

        return $this->render('NteAplicacaoFrigaBundle:edital:form-resultado.html.twig', array(
            'frigaedital' => $frigaEdital,
            'config_form' => $configForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * @param Request $request
     * @param FrigaEdital $frigaEdital
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
            $this->addFlash('info', "Salvo!");
        }

        return $this->render('NteAplicacaoFrigaBundle:edital:form-config-inscricao.html.twig', [
            'frigaedital' => $frigaEdital,
            'config_form' => $configForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }


    /**
     * Creates a form to delete a frigaedital entity.
     * @param FrigaEdital $frigaedital
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(FrigaEdital $frigaedital)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('edital_remover', array('uuid' => $frigaedital->getUuid())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @param Request $request
     * @param FrigaEdital $edital
     * @return RedirectResponse
     */
    public function clonarEditalAction(Request $request, FrigaEdital $edital)
    {

        try {

            $em = $this->getDoctrine()->getManager();

            $editalNovo = new FrigaEdital();

            $editalNovo
                ->setTitulo("Cópia do edital - " . $edital->getTitulo())
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
                ->setDataPublicacaoOficial(new DateTime())
                ->setSituacao(0)
                ->setPublico(0)
                ->setUuid(uniqid())
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
            if (is_null($editalNovo)) {
                $this->addFlash('danger', "Erro ao clonar edital");
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
                if (key_exists($etapa->getId(), $tmpEtapa)) {
                    continue;
                }
                $etapaNova = clone $etapa;
                $etapaNova->setIdEdital($editalNovo);
                $em->persist($etapaNova);
                $em->flush();
                $tmpEtapa[$etapa->getId()] = $etapaNova;

                if (!is_null($etapa->getIdEtapa())) {
                    if (!key_exists($etapa->getIdEtapa()->getId(), $tmpEtapa)) {
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
                if (key_exists($categoria->getId(), $tmpCategoria)) {
                    continue;
                }
                $cc = clone $categoria;
                $cc->setIdEdital($editalNovo);
                $em->persist($cc);
                $em->flush();
                $tmpCategoria[$categoria->getId()] = $cc;

                if (!is_null($categoria->getIdCategoria())) {
                    if (!key_exists($categoria->getIdCategoria()->getId(), $tmpCategoria)) {
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
                        if (array_key_exists($etapa->getId(), $tmpEtapa)) {
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
            $this->addFlash('success', "O edital foi copiado com sucesso!");
        } catch (\Exception $e) {
            $this->addFlash('danger', $e->getMessage());
            $this->addFlash('danger', $e->getLine());
        }
        return $this->redirectToRoute('edital_index_rascunho');
    }

    /**
     * @param Request $request
     * @param FrigaEdital $origem
     * @return RedirectResponse
     */
    public function clonarEtapaAction(Request $request, FrigaEdital $destino, FrigaEdital $origem)
    {
        if (!is_null($origem)
            and !is_null($destino)
            and $origem->getId() != $destino->getId()) {
            try {
                $this->copiarEtapa($origem, $destino);
                $this->addFlash('success', "As etapas foram copiadas com sucesso!");
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage());
                $this->addFlash('danger', $e->getLine());
            }
        }

        return $this->redirectToRoute('edital_etapa', ['uuid' => $destino->getUuid()]);
    }

    /**
     * @param Request $request
     * @param FrigaEdital $origem
     * @return RedirectResponse
     */
    public function clonarPontuacaoAction(Request $request, FrigaEdital $destino, FrigaEdital $origem)
    {
        try {
            $this->copiarPontuacao($origem, $destino);
            $this->addFlash('success', "As etapas foram copiadas com sucesso!");
        } catch (\Exception $e) {
            $this->addFlash('danger', $e->getMessage());
            $this->addFlash('danger', $e->getLine());
        }
        return $this->redirectToRoute('edital_pontuacao', ['uuid' => $destino->getUuid()]);
    }

    /**
     * Remove um edital
     * @param Request $request
     * @param FrigaEdital $edital
     * @return RedirectResponse
     */
    public function removerEditalAction(Request $request, FrigaEdital $edital)
    {

        $em = $this->getDoctrine()->getManager();
        if ($edital->getSituacao() != 0 or $edital->getInscricaoValida()->count()) {
            $this->addFlash('danger', 'Impossível excluir este edital');
            return $this->redirect($request->server->get('HTTP_REFERER'));
        }
        try {
            foreach ($edital->getInscricao() as $item) {
                $em->remove($item);
                $em->flush();
            }
            $em->remove($edital);
            $em->flush();
            $this->addFlash('success', 'Edital removido com sucesso:');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Erro ao excluir edital: ' . $e->getMessage());
        }
        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    /**
     * @param FrigaEdital $origem
     * @param FrigaEdital $destino
     * @return array
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
            if (key_exists($etapa->getId(), $tmpEtapa)) {
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

            if (!is_null($etapa->getIdEtapa())) {
                if (!key_exists($etapa->getIdEtapa()->getId(), $tmpEtapa)) {

                    $ccc = clone $etapa->getIdEtapa();
                    $ccc->setIdEdital($destino);
                    $em->persist($ccc);
                    $em->flush();

                    $ccc->setPR($etapa->getPR(),$destino->getDataPublicacaoOficial());
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
     * @param FrigaEdital $origem
     * @param FrigaEdital $destino
     * @param array $etapas
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
            if (key_exists($categoria->getId(), $tmpCategoria)) {
                continue;
            }
            $cc = clone $categoria;
            $cc->setIdEdital($destino);
            $em->persist($cc);
            $em->flush();
            $tmpCategoria[$categoria->getId()] = $cc;

            if (!is_null($categoria->getIdCategoria())) {
                if (!key_exists($categoria->getIdCategoria()->getId(), $tmpCategoria)) {
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
                    if (!array_key_exists($etapa->getId(), $etapa)) {
                        continue;
                    }
                    $pontuacao->addIdEtapa($etapas[$etapa->getId()]);
                }
            }
            $em->persist($pontuacao);
            $em->flush();
        }
    }

}
