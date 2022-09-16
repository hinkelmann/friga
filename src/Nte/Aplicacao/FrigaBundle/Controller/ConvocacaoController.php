<?php

namespace Nte\Aplicacao\FrigaBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\QueryBuilder;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaConvocacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCargo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCota;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoFeedback;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoPontuacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoPontuacaoAvaliacao;
use Nte\Aplicacao\FrigaBundle\Form\AvaliacaoType;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacaoCategoria;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalConvocacaoType;
use Nte\UsuarioBundle\Entity\Usuario;
use Nte\UsuarioBundle\Form\InscricaoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use stdClass;
use DateTime;
use Exception;

/**
 * Class ConvocacaoController
 * @package Nte\Aplicacao\FrigaBundle\Controller
 */
class ConvocacaoController extends Controller
{

    /**
     * Editais em aberto
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request, $situacao = 50)
    {
        if (!$this->isGranted('ROLE_ADMIN') AND !$this->isGranted('ROLE_AVALIADOR')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }
        $tmp = new ArrayCollection();

        /** @var FrigaEdital $edital */
        foreach ($this->editais() as $edital) {
            $x = $edital->getEtapa()->filter(Function (FrigaEditalEtapa $etapa) use ($situacao) {
                if ($situacao == 50) {
                    return $etapa->getTipo() == 5 and $etapa->getPeriodoHabilitado();
                } else if ($situacao == 100) {
                    return $etapa->getTipo() == 5
                        and $etapa->getPeriodoHabilitado() == false
                        and $etapa->getAndamentoPrazo() == 100;
                } else if ($situacao == 0) {
                    return $etapa->getTipo() == 5
                        and $etapa->getPeriodoHabilitado() == false
                        and $etapa->getAndamentoPrazo() == 0;
                }
            });
            $tmp = new ArrayCollection(array_merge($tmp->toArray(), $x->toArray()));
        }
        return $this->render('NteAplicacaoFrigaBundle:convocacao:index.html.twig', [
            'editais' => $tmp,
        ]);
    }

    public function exportarCsvAction(Request $request, FrigaEditalEtapa $etapa)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var QueryBuilder $convocacao */
        $convocacao = $em
            ->getRepository(FrigaConvocacao::class)
            ->createQueryBuilder('c')
            ->innerJoin('c.idInscricao', 'i')
            ->where('c.idEtapa = :etapa')
            ->setParameter('etapa', $etapa)
            ->orderBy('c.data', 'asc')
            ->addOrderBy('i.nome', 'asc')
            ->getQuery()->getResult();

        $arquivo = "/tmp/agendamento-" . $etapa->getId() . ".csv";
        if (is_file($arquivo)) {
            unlink($arquivo);
        }

        $out = fopen($arquivo, 'w');
        fputcsv($out, [
            'inscricao',
            'nome',
            'data',
            "hora",
            "local",
        ]);
        /** @var FrigaConvocacao $p */
        foreach ($convocacao as $p) {
            fputcsv($out, [
                $p->getIdInscricao()->getUuid(),
                $p->getIdInscricao()->getNome(),
                $p->getData()->format('d/m/Y'),
                $p->getData()->format('H:i:s'),
                $p->getObservacao(),
            ]);
        }
        fclose($out);
        return $this->file($arquivo);
    }

    public function impresaoPresencaAction(Request $request, FrigaEditalEtapa $etapa)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var QueryBuilder $convocacao */
        $convocacao = $em
            ->getRepository(FrigaConvocacao::class)
            ->createQueryBuilder('c')
            ->innerJoin('c.idInscricao', 'i')
            ->where('c.idEtapa = :etapa')
            ->setParameter('etapa', $etapa)
            ->orderBy('c.data', 'asc')
            ->addOrderBy('i.nome', 'asc')
            ->getQuery()->getResult();

        return $this->render('NteAplicacaoFrigaBundle:convocacao:impressao-presenca.html.twig', [
            'etapa' => $etapa,
            'convocacao' => new ArrayCollection($convocacao),
        ]);
    }

    public function impresaoRelacaoAction(Request $request, FrigaEditalEtapa $etapa)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var QueryBuilder $convocacao */
        $convocacao = $em
            ->getRepository(FrigaConvocacao::class)
            ->createQueryBuilder('c')
            ->innerJoin('c.idInscricao', 'i')
            ->where('c.idEtapa = :etapa')
            ->setParameter('etapa', $etapa)
            ->orderBy('c.data', 'asc')
            ->addOrderBy('i.nome', 'asc')
            ->getQuery()->getResult();

        return $this->render('NteAplicacaoFrigaBundle:convocacao:impressao-relacao.html.twig', [
            'etapa' => $etapa,
            'convocacao' => new ArrayCollection($convocacao),
        ]);
    }

    /**
     * @param Request $request
     * @param FrigaEdital $edital
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function indexCandidatoAction(Request $request, FrigaEditalEtapa $etapa)
    {
        if (!$this->isGranted('ROLE_ADMIN') and !$this->isGranted('ROLE_AVALIADOR')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }

        $classificacao = new ArrayCollection();

        $edital = $etapa->getIdEdital();
        $geral = $edital->getInscricaoValida()
            ->filter(function (FrigaInscricao $inscricao) {
                return $inscricao->getIdSituacao() != 0
                    and $inscricao->getIdSituacao() != 1
                    and $inscricao->getIdSituacao() != 3
                    and $inscricao->getIdSituacao() != 5;
            })->getIterator();
        $geral->uasort(function (FrigaInscricao $a, FrigaInscricao $b) {
            return $b->getPontuacaoSoma(true) <=> $a->getPontuacaoSoma(true);
        });
        $geral = new ArrayCollection($geral->getArrayCopy());


        if ($edital->isResultado0() or $edital->isResultado1()) {
            /** @var FrigaEditalCargo $cargo */
            foreach ($edital->getCargo() as $cargo) {
                if ($edital->isResultado0()) {
                    if ($edital->getCota()->count()) {
                        /** @var FrigaEditalCota $lista */
                        foreach ($edital->getCota() as $lista) {
                            $obj = new stdClass();
                            $obj->nome = $cargo->getDescricao() . "/" . $lista->getDescricao();
                            $obj->cargo = $cargo;
                            $obj->lista = $lista;
                            $obj->classificacao = $geral->filter(function (FrigaInscricao $inscricao) use ($cargo, $lista) {
                                return $inscricao->getIdCargo()->getId() == $cargo->getId()
                                    and $inscricao->getIdCota()->getId() == $lista->getId();
                            });
                            $classificacao->add($obj);
                        }
                    }
                }
                if ($edital->isResultado1()) {
                    $obj = new stdClass();
                    $obj->nome = "Classificação Geral / " . $cargo->getDescricao();
                    $obj->cargo = $cargo;
                    $obj->lista = null;
                    $obj->classificacao = $geral->filter(function (FrigaInscricao $inscricao) use ($cargo) {
                        return $inscricao->getIdCargo()->getId() == $cargo->getId();
                    });
                    $classificacao->add($obj);
                }
            }
        }
        if ($edital->isResultado2()) {
            foreach ($edital->getCota() as $lista) {
                $obj = new stdClass();
                $obj->nome = "Classificação Geral/" . $lista->getDescricao();
                $obj->cargo = null;
                $obj->lista = $lista;
                $obj->classificacao = $geral->filter(function (FrigaInscricao $inscricao) use ($lista) {
                    return $inscricao->getIdCota()->getId() == $lista->getId();
                });
                $classificacao->add($obj);
            }
        }
        if ($edital->isResultado3()) {
            $obj = new stdClass();
            $obj->nome = "Classificação Geral";
            $obj->cargo = null;
            $obj->lista = null;
            $obj->classificacao = $geral;
            $classificacao->add($obj);
        }
        return $this->render('NteAplicacaoFrigaBundle:convocacao:index-candidato.html.twig', [
            'classificao' => $classificacao,
            'edital' => $edital,
            'etapa' => $etapa,
        ]);
    }

    /**
     * Traaz todos os editais que usuário pode ter acesso;
     * Se administrador, traz todos os editais.
     *
     * @param int $situacao
     * @return array|ArrayCollection|object[]
     */
    public function editais($situacao = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $editais = new ArrayCollection();
        if ($this->isGranted('ROLE_ADMIN')) {
            $editais = $em->getRepository(FrigaEdital::class)->findAll();
        } else if ($this->isGranted('ROLE_AVALIADOR')) {

            /** @var ArrayCollection $tmp */
            $tmp = $this->getUser()
                ->getIdEditalUsuario()
               // ->filter(function (FrigaEditalUsuario $eu) use ($situacao) {
                //    return $eu->getIdEdital()->getSituacao() == $situacao;
                //})
            ;
            if ($tmp->count()) {
                /** @var FrigaEditalUsuario $eu */
                foreach ($tmp as $eu) {
                    if (!$editais->contains($eu->getIdEdital())) {
                        $editais->add($eu->getIdEdital());
                    }
                };
                $editais = $editais->filter(function (FrigaEdital $edital) {
                    return $edital->getEtapa()->filter(function (FrigaEditalEtapa $etapa) {
                        return true;//$etapa->getPeriodoHabilitado();
                    })->count();
                });
            }
        }
        return $editais;
    }


    /**
     * @param Request $request
     * @param FrigaEditalEtapa $etapa
     * @param FrigaInscricao $inscricao
     * @return RedirectResponse|Response
     */
    public function formAction(Request $request, FrigaEditalEtapa $etapa, FrigaInscricao $inscricao)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            if (!$this->isGranted('ROLE_AVALIADOR')) {
                return $this->redirectToRoute('nte_admin_painel_homepage');
            }
            if (!$etapa->getPeriodoHabilitado()) {
                return $this->redirectToRoute('avaliacao_index');
            }
        }
        $em = $this->getDoctrine()->getManager();
        if ($inscricao->getConvocacaoEtapa($etapa)->count()) {
            $convocacao = $inscricao->getConvocacaoEtapa($etapa)->first();
        } else {
            $convocacao = new FrigaConvocacao();
            $convocacao->setIdEtapa($etapa)
                ->setIdInscricao($inscricao);
        }

        $form = $this->createForm(FrigaEditalConvocacaoType::class, $convocacao);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $convocacao->setIdUsuario($this->getUser());
            $em->persist($convocacao);
            $em->flush();
            $this->addFlash('success', "Agendamento realizado com sucesso!");
            return $this->redirectToRoute('convocacao_etapa', ['etapa' => $etapa->getId()]);
        }

        if ($etapa->getFinal()) {
            $tema = 'NteAplicacaoFrigaBundle:convocacao:form-final.html.twig';
        } else {
            $tema = 'NteAplicacaoFrigaBundle:convocacao:form.html.twig';
        }

        return $this->render($tema, [
            'form' => $form->createView(),
            'etapa' => $etapa,
            'convocacao' => $convocacao,
            'inscricao' => $inscricao,
        ]);
    }
}