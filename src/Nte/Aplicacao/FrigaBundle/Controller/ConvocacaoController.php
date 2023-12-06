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
use Doctrine\ORM\QueryBuilder;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaConvocacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCargo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCota;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapaUsuario;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao;
use Nte\Aplicacao\FrigaBundle\Form\ExportarMoodleType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalConvocacaoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ConvocacaoController.
 */
class ConvocacaoController extends Controller
{
    use ClassificacaoTrait;

    /**
     * Editais em aberto.
     *
     * @return Response
     */
    public function indexAction(Request $request, $situacao = 50)
    {
        if (!$this->isGranted('ROLE_ADMIN') and !$this->isGranted('ROLE_AVALIADOR')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }
        $tmp = new ArrayCollection();

        /** @var FrigaEdital $edital */
        foreach ($this->editais() as $edital) {
            $x = $edital->getEtapa()->filter(function(FrigaEditalEtapa $etapa) use ($situacao) {
                if (50 == $situacao) {
                    return 5 == $etapa->getTipo() and $etapa->getPeriodoHabilitado();
                } elseif (100 == $situacao) {
                    return 5 == $etapa->getTipo()
                        and false == $etapa->getPeriodoHabilitado()
                        and 100 == $etapa->getAndamentoPrazo();
                } elseif (0 == $situacao) {
                    return 5 == $etapa->getTipo()
                        and false == $etapa->getPeriodoHabilitado()
                        and 0 == $etapa->getAndamentoPrazo();
                }
            });
            $tmp = new ArrayCollection(\array_merge($tmp->toArray(), $x->toArray()));
        }

        return $this->render('NteAplicacaoFrigaBundle:convocacao:index.html.twig', [
            'editais' => $tmp,
        ]);
    }

    public function exportarMoodleAction(Request $request, FrigaEditalEtapa $etapa)
    {
        $form = $this->createForm(ExportarMoodleType::class, null, ['edital' => $etapa->getIdEdital()]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();

            /** @var ArrayCollection $idCargo */
            $idCargo = $form->get('idCargo')->getData();

            /** @var ArrayCollection $idCota */
            $idCota = $form->get('idCota')->getData();

            $idCargo = $idCargo->map(function(FrigaEditalCargo $c) {
                return $c->getId();
            })->toArray();

            $idCota = $idCota->map(function(FrigaEditalCota $c) {
                return $c->getId();
            })->toArray();

            $geral = $em->createQueryBuilder()
                ->select('i')
                ->from(FrigaInscricao::class, 'i')
                ->where('i.idSituacao not in (:situacao)')
                ->setParameter('situacao', [-999, -10, -1, 0, 1, 3, 5])
                ->andWhere('i.idEdital = :edital')
                ->setParameter('edital', $etapa->getIdEdital())
                ->andWhere('i.idCota in (:idCota)')
                ->setParameter('idCota', $idCota)
                ->andWhere('i.idCargo in (:idCargo)')
                ->setParameter('idCargo', $idCargo)
                ->orderBy('i.nome', 'asc')
                ->getQuery()
                ->getResult();
            $tmp = [];
            /** @var FrigaInscricao $item */
            foreach ($geral as $item) {
                $obj = new \stdClass();
                $obj->name = $item->getNome();
                $obj->username = \str_replace(['.', '-'], null, $item->getCpf());
                $obj->email = $item->getContatoEmail();
                $obj->auth = 'manual';
                $tmp[] = $obj;
            }

            $obj = new \stdClass();
            $obj->name = $etapa->getIdEdital()->getTitulo();
            $obj->url = $this->generateUrl('nte_site_edital', ['id' => $etapa->getIdEdital()->getId(), 'url' => $etapa->getIdEdital()->getUrl()]);
            $obj->timestart = $form->get('dataInicial')->getData()->getTimestamp();
            $obj->timeend = $form->get('dataFinal')->getData()->getTimestamp();
            $obj->roleid = $form->get('papel')->getData();
            $obj->course = $form->get('curso')->getData();
            $obj->users = $tmp;
            $obj = \base64_encode(\json_encode($obj));

            return $this->redirect($form->get('ambiente')->getData() . '/local/venom/api.php?obj=' . $obj);
            //exit();
        }

        return $this->render('NteAplicacaoFrigaBundle:convocacao:form-moodle.html.twig', [
            'form' => $form->createView(),
            'etapa' => $etapa,
            'edital' => $etapa->getIdEdital(),
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

        $arquivo = '/tmp/agendamento-' . $etapa->getId() . '.csv';
        if (\is_file($arquivo)) {
            \unlink($arquivo);
        }

        $out = \fopen($arquivo, 'w');
        \fputcsv($out, [
            'inscricao',
            'nome',
            'cpf',
            'email',
            'data',
            'hora',
            'local',
        ]);
        /** @var FrigaConvocacao $p */
        foreach ($convocacao as $p) {
            \fputcsv($out, [
                $p->getIdInscricao()->getUuid(),
                $p->getIdInscricao()->getNome(),
                $p->getIdInscricao()->getCpf(),
                $p->getIdInscricao()->getContatoEmail(),
                \is_null($p->getData()) ? '' : $p->getData()->format('d/m/Y'),
                \is_null($p->getData()) ? '' : $p->getData()->format('H:i:s'),
                $p->getObservacao(),
            ]);
        }
        \fclose($out);

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

    public function impresaoRelacaoContatoAction(Request $request, FrigaEditalEtapa $etapa)
    {
        if (!$this->isGranted('ROLE_ADMIN') and !$this->isGranted('ROLE_AVALIADOR')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }

        $classificacao = new ArrayCollection();

        $edital = $etapa->getIdEdital();
        $geral = $edital->getInscricaoValida()
            ->filter(function(FrigaInscricao $inscricao) {
                return 0 != $inscricao->getIdSituacao()
                    and 1 != $inscricao->getIdSituacao()
                    and 3 != $inscricao->getIdSituacao()
                    and 5 != $inscricao->getIdSituacao();
            })->getIterator();
        $geral->uasort(function(FrigaInscricao $a, FrigaInscricao $b) {
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
                            $obj = new \stdClass();
                            $obj->nome = $cargo->getDescricao() . '/' . $lista->getDescricao();
                            $obj->cargo = $cargo;
                            $obj->lista = $lista;
                            $obj->classificacao = $geral->filter(function(FrigaInscricao $inscricao) use ($cargo, $lista) {
                                return $inscricao->getIdCargo()->getId() == $cargo->getId()
                                    and $inscricao->getIdCota()->getId() == $lista->getId();
                            });
                            $classificacao->add($obj);
                        }
                    }
                }
                if ($edital->isResultado1()) {
                    $obj = new \stdClass();
                    $obj->nome = 'Classificação Geral / ' . $cargo->getDescricao();
                    $obj->cargo = $cargo;
                    $obj->lista = null;
                    $obj->classificacao = $geral->filter(function(FrigaInscricao $inscricao) use ($cargo) {
                        return $inscricao->getIdCargo()->getId() == $cargo->getId();
                    });
                    $classificacao->add($obj);
                }
            }
        }
        if ($edital->isResultado2()) {
            foreach ($edital->getCota() as $lista) {
                $obj = new \stdClass();
                $obj->nome = 'Classificação Geral/' . $lista->getDescricao();
                $obj->cargo = null;
                $obj->lista = $lista;
                $obj->classificacao = $geral->filter(function(FrigaInscricao $inscricao) use ($lista) {
                    return $inscricao->getIdCota()->getId() == $lista->getId();
                });
                $classificacao->add($obj);
            }
        }
        if ($edital->isResultado3()) {
            $obj = new \stdClass();
            $obj->nome = 'Classificação Geral';
            $obj->cargo = null;
            $obj->lista = null;
            $obj->classificacao = $geral;
            $classificacao->add($obj);
        }

        return $this->render('NteAplicacaoFrigaBundle:convocacao:impressao-contato.html.twig', [
            'classificao' => $classificacao,
            'edital' => $edital,
            'etapa' => $etapa,
        ]);
    }

    /**
     * @return RedirectResponse|Response
     *
     * @throws \Exception
     */
    public function indexCandidatoAction(Request $request, FrigaEditalEtapa $etapa)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            if (!$this->getUser()->getPermissaoEtapa($etapa)) {
                $this->addFlash('danger', 'Você não tem permissão para realizar a convocação!');

                return $this->redirectToRoute('nte_admin_painel_homepage');
            }
            if (!$etapa->getPeriodoHabilitado()) {
                $this->addFlash('danger', 'Convocação fora do período.');

                return $this->redirectToRoute('nte_admin_painel_homepage');
            }
        }

        return $this->render('NteAplicacaoFrigaBundle:convocacao:index-candidato.html.twig', [
            'classificacao' => $this->gerarObjetoClassificacao($etapa->getIdEtapaClassificacao()),
            'edital' => $etapa->getIdEdital(),
            'etapa' => $etapa,
        ]);
    }

    /**
     * Traaz todos os editais que usuário pode ter acesso;
     * Se administrador, traz todos os editais.
     *
     * @param int $situacao
     *
     * @return array|ArrayCollection|object[]
     */
    public function editais($situacao = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $editais = new ArrayCollection();
        if ($this->isGranted('ROLE_ADMIN')) {
            $editais = $em->getRepository(FrigaEdital::class)->findAll();
        } elseif ($this->isGranted('ROLE_AVALIADOR')) {
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
                }
                $editais = $editais->filter(function(FrigaEdital $edital) {
                    return $edital->getEtapa()->filter(function(FrigaEditalEtapa $etapa) {
                        return true; //$etapa->getPeriodoHabilitado();
                    })->count();
                });
            }
        }

        return $editais;
    }

    /**
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
            $etapaUsuario = new FrigaEditalEtapaUsuario($etapa, $inscricao, $this->getUser());
            $em->persist($etapaUsuario);

            $convocacao->setIdUsuario($this->getUser());
            $em->persist($convocacao);
            $em->flush();

            $this->addFlash('success', 'Agendamento realizado com sucesso!');

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
