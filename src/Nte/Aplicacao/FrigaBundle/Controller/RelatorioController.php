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
use Exception;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCargo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao;
use Nte\Aplicacao\FrigaBundle\Entity\Log;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RelatorioController extends Controller
{
    private $mQuantitativo;
    private $mQuantitativoCargo;
    private $mString;

    /**
     * RelatorioController constructor.
     */
    public function __construct()
    {
        $this->mQuantitativo = new \stdClass();
        $this->mQuantitativo->inscricao = 0;
        $this->mQuantitativo->homologacaoNegativa = 0;
        $this->mQuantitativo->homologacao = 0;
        $this->mQuantitativo->desclassificacao = 0;
        $this->mQuantitativo->avaliacao = 0;
        $this->mQuantitativo->recurso = 0;
        $this->mQuantitativo->classificacao = 0;
        $this->mQuantitativo->convocacao = 0;
        $this->mQuantitativo->cargo = 0;
        $this->mQuantitativo->pontuacao = 0;
        $this->mQuantitativo->edital = 0;
        $this->mQuantitativo->recursos = 0;
        $this->mQuantitativo->recursosDeferidos = 0;
        $this->mQuantitativo->recursosIndeferidos = 0;
        $this->mQuantitativo->etapaAvaliacao = 0;
        $this->mQuantitativo->etapaAvaliacaoConcluida = 0;
        $this->mQuantitativo->etapaClassificacao = 0;
        $this->mQuantitativo->etapaClassificacaoConcluida = 0;

        $this->mQuantitativoCargo = new \stdClass();
        $this->mQuantitativoCargo->inscricao = 0;
        $this->mQuantitativoCargo->homologacaoNegativa = 0;
        $this->mQuantitativoCargo->homologacao = 0;
        $this->mQuantitativoCargo->desclassificacao = 0;
        $this->mQuantitativoCargo->avaliacao = 0;
        $this->mQuantitativoCargo->recurso = 0;
        $this->mQuantitativoCargo->classificacao = 0;
        $this->mQuantitativoCargo->convocacao = 0;

        $this->mString = [
            'inscricao' => 'Inscrições Realizadas',
            'homologacao' => 'Inscrições Homologadas',
            'homologacaoNegativa' => 'Inscrições Não Homologadas',
            'avaliacao' => 'Inscrições em Avaliação',
            'recurso' => 'Inscrições com Recurso',
            'classificacao' => 'Classificados',
            'desclassificacao' => 'Desclassificados',
            'convocacao' => 'Convocados',
            'recursos' => 'Recursos sem avaliação',
            'recursosDeferidos' => 'Recurso Defirido',
            'recursosIndeferidos' => 'Recurso Indeferido',
        ];
    }

    /***
     * @return Response
     * @throws Exception
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $editais = new ArrayCollection($em->getRepository(FrigaEdital::class)->findAll());
        if ($this->isGranted('ROLE_ADMIN_EDITAL')) {
            $editais = new ArrayCollection($em->getRepository(FrigaEdital::class)->findAll());
        } elseif ($this->isGranted('ROLE_GERENCIAL')) {
            $editais = new ArrayCollection();

            /** @var FrigaEditalUsuario $eu */
            foreach ($this->getUser()->getIdEditalUsuario() as $eu) {
                $editais->add($eu->getIdEdital());
            }
        }
        $editais = $editais->filter(function(FrigaEdital $edital) {
            return $edital->getSituacao() > 0;
        });
        $editais = $editais->getIterator();
        $editais->uasort(function(FrigaEdital $a, FrigaEdital $b) {
            return $b->getDataPublicacaoOficial() <=> $a->getDataPublicacaoOficial();
        });

        return $this->render('@NteAplicacaoFriga/relatorio/index.html.twig', [
            'editais' => $editais,
        ]);
    }

    /**
     * @return Response
     */
    public function indexResumoAction()
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('relatorio_index');
        }

        return $this->render('@NteAplicacaoFriga/relatorio/index-resumo.html.twig', [
            'editalCargo' => $this->getEditalCargo($this->getEditaisSituacao(), true),
            'editalCargoPilha' => $this->getEditalCargoACargo($this->getEditaisSituacao(), true),
            'editalCargoPilhaSeparada' => $this->getEditalCargoOdenadoSeparado($this->getEditaisSituacao()),
            'editalQuantitativo' => $this->getQuantitativoCargoEdtiais($this->getEditaisSituacao()),
            'editalQuantitativoSerie' => $this->getQuantitativoEdtiaisSerie($this->getEditaisSituacao()),
            'editais' => $this->getEditaisTituloArray($this->getEditaisSituacao()->toArray()),
        ]);
    }

    /**
     * @return Response
     */
    public function resumoAction(Request $request, FrigaEdital $frigaEdital)
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('@NteAplicacaoFriga/relatorio/resumo.html.twig', [
            'edital' => $frigaEdital,
            'cargoLista' => $this->getCargoTituloArray($frigaEdital),
            'cargoPilha' => $this->getQuantitativoCargo($frigaEdital),
            'editalCargo' => $this->getEditalCargo([$frigaEdital], true),
            'editalCargoPilha' => $this->getEditalCargoACargo([$frigaEdital], true),
            'editalCargoPilhaSeparada' => $this->getEditalCargoOdenadoSeparado([$frigaEdital]),
            'editalQuantitativo' => $this->getQuantitativoCargoEdtiais([$frigaEdital]),
            'editalQuantitativoSerie' => $this->getQuantitativoEdtiaisSerie([$frigaEdital]),
            'editais' => $this->getEditaisTituloArray([$frigaEdital]),
        ]);
    }

    /**
     * @param int $situacao
     *
     * @return ArrayCollection
     */
    private function getEditaisSituacao($situacao = 1)
    {
        return new ArrayCollection($this->getDoctrine()->getManager()
            ->getRepository(FrigaEdital::class)
            ->findBy(['situacao' => $situacao]));
    }

    private function getEditaisTituloArray($editais)
    {
        return \array_map(function(FrigaEdital $edital) {
            return $edital->getTitulo();
        }, $editais);
    }

    /**
     * @return array
     */
    private function getCargoTituloArray(FrigaEdital $edital)
    {
        return $edital->getCargo()->map(function(FrigaEditalCargo $cargo) {
            return $cargo->getDescricao();
        })->toArray();
    }

    /**
     * @param ArrayCollection $editais
     * @param bool $drilldown
     *
     * @return array|\stdClass
     */
    private function getEditalCargo($editais, $drilldown = false)
    {
        if ($drilldown) {
            $tmp = new \stdClass();
            $tmp->name = 'Editais';
            $tmp->colorByPoint = 'Editais';
            $tmp->data = [];
            $tmp = [$tmp];
        } else {
            $tmp = [];
        }
        /** @var FrigaEdital $edital */
        foreach ($editais as $edital) {
            $soma = 0;
            /** @var FrigaEditalCargo $cargo */
            foreach ($edital->getCargo() as $cargo) {
                $soma += $cargo->getInscricaoValida()->count();
            }
            if ($drilldown) {
                $obj = new \stdClass();
                $obj->name = $edital->getTitulo();
                $obj->y = $soma;
                $obj->drilldown = $edital->getId();
                $tmp[0]->data[] = $obj;
            } else {
                $tmp[] = [$edital->getTitulo(), $soma];
            }
        }

        return $tmp;
    }

    /**
     * @return mixed
     */
    public function getEditalCargoOdenadoSeparado($editais)
    {
        $tmp0 = $this->getEditalCargoACargoComTitulo($editais);
        \usort($tmp0, function($a, $b) {
            return $b[1] <=> $a[1];
        });
        $tmp[0] = \array_filter($tmp0, function($a) {
            return $a[1] > 0;
        });
        $tmp[1] = \array_filter($tmp0, function($a) {
            return $a[1] <= 0;
        });
        if (\count($tmp[1])) {
            $aux = [];
            foreach ($tmp[1] as $item) {
                if (\array_key_exists($item[3], $aux)) {
                    $aux[$item[3]]->cargo[] = $item[0];
                } else {
                    $obj = new \stdClass();
                    $obj->id = $item[3];
                    $obj->cargo = [$item[0]];
                    $obj->edital = $item[2];
                    $aux[$item[3]] = $obj;
                }
            }
            $tmp[1] = $aux;
        }

        return $tmp;
    }

    /**
     * @param ArrayCollection $editais
     * @param bool $drilldown
     *
     * @return array
     */
    private function getEditalCargoACargo($editais, $drilldown = false)
    {
        $tmp1 = [];
        /** @var FrigaEdital $edital */
        foreach ($editais as $edital) {
            $tmp0 = [];
            /** @var FrigaEditalCargo $cargo */
            foreach ($edital->getCargo() as $cargo) {
                $tmp0[] = [$cargo->getDescricao(), $cargo->getInscricaoValida()->count()];
            }
            if ($drilldown) {
                $obj = new \stdClass();
                $obj->id = $edital->getId();
                $obj->name = $edital->getTitulo();
                $obj->data = $tmp0;
                $tmp1[] = $obj;
            } else {
                $tmp1 = \array_merge($tmp1, $tmp0);
            }
        }

        return $tmp1;
    }

    /**
     * @param ArrayCollection $editais
     * @param bool $drilldown
     *
     * @return array
     */
    private function getEditalCargoACargoComTitulo($editais, $drilldown = false)
    {
        $tmp = [];
        /** @var FrigaEdital $edital */
        foreach ($editais as $edital) {
            /** @var FrigaEditalCargo $cargo */
            foreach ($edital->getCargo() as $cargo) {
                $tmp[] = [$cargo->getDescricao(), $cargo->getInscricaoValida()->count(), $edital->getTitulo(), $edital->getId()];
            }
        }

        return $tmp;
    }

    /**
     * @param ArrayCollection $editais
     *
     * @return array
     */
    private function getEditalCargoACargoPilha($editais)
    {
        $tmp = [];
        /** @var FrigaEdital $edital */
        foreach ($editais as $edital) {
            $obj = new \stdClass();
            $obj->name = $edital->getDescricao();
            $obj->id = $edital->getId();
            /** @var FrigaEditalCargo $cargo */
            foreach ($edital->getCargo() as $cargo) {
                $obj->data[] = [$cargo->getDescricao(), $cargo->getInscricaoValida()->count()];
            }
            $tmp[] = $obj;
        }

        return $tmp;
    }

    /**
     * @param ArrayCollection $editais
     *
     * @return array
     */
    private function getQuantitativoEdtiaisSerie($editais)
    {
        $tmp = [];
        $item = $this->getQuantitativoCargoEdtiais($editais);

        $obj = new \stdClass();
        $obj->name = 'Inscrições Realizadas';
        $obj->y = $item->inscricao;
        $tmp[0][] = $obj;

        $obj = new \stdClass();
        $obj->name = 'Inscrições Homologadas';
        $obj->y = $item->homologacao;
        $tmp[0][] = $obj;

        $obj = new \stdClass();
        $obj->name = 'Inscrições Não Homologadas';
        $obj->y = $item->homologacaoNegativa;
        $tmp[0][] = $obj;

        $obj = new \stdClass();
        $obj->name = 'Inscrições em Avaliação';
        $obj->y = $item->avaliacao;
        $tmp[0][] = $obj;

        $obj = new \stdClass();
        $obj->name = 'Inscrições com Recurso';
        $obj->y = $item->recurso;
        $tmp[0][] = $obj;

        $obj = new \stdClass();
        $obj->name = 'Classificados';
        $obj->y = $item->classificacao;
        $tmp[0][] = $obj;

        $obj = new \stdClass();
        $obj->name = 'Desclassificados';
        $obj->y = $item->desclassificacao;
        $tmp[0][] = $obj;

        $obj = new \stdClass();
        $obj->name = 'Convocados';
        $obj->y = $item->convocacao;
        $tmp[0][] = $obj;

        $obj = new \stdClass();
        $obj->name = 'Recursos sem avaliação';
        $obj->y = $item->recursos;
        $tmp[1][] = $obj;

        $obj = new \stdClass();
        $obj->name = 'Recurso Deferido';
        $obj->y = $item->recursosDeferidos;
        $tmp[1][] = $obj;

        $obj = new \stdClass();
        $obj->name = 'Recurso Indeferido';
        $obj->y = $item->recursosIndeferidos;
        $tmp[1][] = $obj;

        return $tmp;
    }

    /**
     * @param ArrayCollection $editais
     *
     * @return \stdClass
     */
    private function getQuantitativoCargoEdtiais($editais)
    {
        $tmp0 = new ArrayCollection();
        /** @var FrigaEdital $edital */
        foreach ($editais as $edital) {
            $tmp0->add($this->getQuantitativo($edital));
        }
        $tmp1 = clone $this->mQuantitativo;
        foreach ($tmp0->toArray() as $item) {
            foreach (\get_object_vars($this->mQuantitativo) as $chave => $valor) {
                $tmp1->$chave += $item->$chave;
            }
        }

        return $tmp1;
    }

    /**
     * @return array
     */
    private function getQuantitativoCargo(FrigaEdital $edital)
    {
        $tmp0 = new ArrayCollection();
        /** @var FrigaEditalCargo $cargo */
        foreach ($edital->getCargo() as $cargo) {
            $tmp0->add($this->getQuantitativo($edital, $cargo));
        }

        $tmp1 = [];
        foreach (\get_object_vars($this->mQuantitativoCargo) as $chave => $valor) {
            $obj = new \stdClass();
            $obj->name = $this->mString[$chave];
            $obj->data = [];
            foreach ($tmp0->toArray() as $item) {
                $obj->data[] = $item->$chave;
            }
            $tmp1[] = $obj;
        }

        return $tmp1;
    }

    /**
     * @return \stdClass
     */
    private function getQuantitativo(FrigaEdital $edital, FrigaEditalCargo $cargo = null)
    {
        $obj = clone $this->mQuantitativo;
        $obj->inscricao = $edital->getInscricaoSituacao(0, $cargo)->count();
        $obj->homologacaoNegativa = $edital->getInscricaoSituacao(1, $cargo)->count();
        $obj->homologacao = $edital->getInscricaoSituacao(2, $cargo)->count();
        $obj->desclassificacao = $edital->getInscricaoSituacao(3, $cargo)->count();
        $obj->avaliacao = $edital->getInscricaoSituacao(4, $cargo)->count();
        $obj->recurso = $edital->getInscricaoSituacao(5, $cargo)->count();
        $obj->classificacao = $edital->getInscricaoSituacao(6, $cargo)->count();
        $obj->convocacao = $edital->getInscricaoSituacao(7, $cargo)->count();

        if (!$cargo) {
            $obj->cargo = $edital->getCargo()->count();
            $obj->pontuacao = 0;
            $obj->edital = 1;
            $obj->recursos = $edital->getRecursosSituacao(0)->count();
            $obj->recursosDeferidos = $edital->getRecursosSituacao(1)->count();
            $obj->recursosIndeferidos = $edital->getRecursosSituacao(-1)->count();
        }

        return $obj;
    }

    /**
     * @return Response
     */
    public function perfilAction(Request $request, FrigaInscricao $inscricao)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $logs = $em->createQueryBuilder()
            ->select('l')
            ->from(Log::class, 'l')
           // ->where("l.metodo = :metodo")
            ->andWhere('l.uri like :uri')
            ->orderBy('l.id', 'desc')
            ->setParameter('uri', "%{$inscricao->getUuid()}%")
            //->setParameter('metodo',"POST")
            ->getQuery()
            ->getResult();

        return $this->render('@NteAplicacaoFriga/relatorio/perfil.html.twig', [
            'inscricao' => $inscricao,
            'logs' => $logs,
        ]);
    }

    /**
     * @return Response
     *
     * @throws \Exception
     */
    public function todosInscritosAction(Request $request)
    {
        $editais = $this->getEditaisSituacao();
        $pessoas = [];
        /** @var FrigaEdital $edital */
        foreach ($editais as $edital) {
            $pessoas = \array_merge($edital->getInscricaoValida()->toArray(), $pessoas);
        }

        return $this->render('@NteAplicacaoFriga/relatorio/todos-inscricao.html.twig', [
            'inscricoes' => $pessoas,
        ]);
    }

    /**
     * @return Response
     */
    public function todosAnuladosAction(Request $request)
    {
        $editais = $this->getEditaisSituacao();
        $pessoas = [];
        /** @var FrigaEdital $edital */
        foreach ($editais as $edital) {
            $pessoas = \array_merge($edital->getInscricaoSituacao(-999)->toArray(), $pessoas);
        }

        return $this->render('@NteAplicacaoFriga/relatorio/todos-anulados.html.twig', [
            'inscricoes' => $pessoas,
        ]);
    }

    /**
     * @return Response
     */
    public function incritosAction(Request $request, FrigaEdital $edital)
    {
        return $this->render('@NteAplicacaoFriga/relatorio/inscricao.html.twig', [
            'edital' => $edital,
        ]);
    }

    /**
     * @return Response
     */
    public function anuladosAction(Request $request, FrigaEdital $edital)
    {
        return $this->render('@NteAplicacaoFriga/relatorio/anulados.html.twig', [
            'edital' => $edital,
        ]);
    }

    /**
     * @return Response
     */
    public function recursoAction(Request $request, FrigaEdital $editais = null)
    {
        $tmp = new ArrayCollection();

        $editais = $this->getEditaisSituacao();

        if ($editais->count()) {
            /** @var FrigaEdital $edital */
            foreach ($editais as $edital) {
                $tmp = new ArrayCollection(\array_merge($tmp->toArray(), $edital->getRecursos()->toArray()));
            }
        }

        return $this->render('@NteAplicacaoFriga/relatorio/recurso.html.twig', [
            'recursos' => $tmp,
        ]);
    }

    /**
     * @return Response
     */
    public function convocacoAction(Request $request, FrigaEdital $editais = null)
    {
        $tmp = new ArrayCollection();
        $editais = $this->getEditaisSituacao();

        if ($editais->count()) {
            /** @var FrigaEdital $edital */
            foreach ($editais as $edital) {
                /** @var FrigaInscricao $incricao */
                foreach ($edital->getInscricao() as $incricao) {
                    $tmp = new ArrayCollection(\array_merge($tmp->toArray(), $incricao->getConvocacao()->toArray()));
                }
            }
        }

        return $this->render('@NteAplicacaoFriga/relatorio/convocacao.html.twig', [
            'convocacoes' => $tmp,
        ]);
    }

    /**
     * @return Response
     */
    public function recursoSituacaoAction(Request $request, $id)
    {
        $editais = $this->getDoctrine()
            ->getManager()
            ->getRepository(FrigaEdital::class)
            ->findAll();

        $pessoas = $this->getDoctrine()
            ->getManager()
            ->createQueryBuilder()
            ->select('fp')
            ->from(FrigaPessoa::class, 'fp')
            ->where('fp.idSituacao <>  -99 or fp.idSituacao is null')
            ->orderBy('fp.nome', 'asc')
            ->getQuery()->getResult();

        return $this->render('NteAplicacaoCadastrosBundle:frigarelatorio:recurso.html.twig', [
            'frigaEdital' => $editais,
            'frigaPessoas' => $pessoas,
        ]);
    }

    public function incritosPorSituacaoAction(Request $request, $situacao)
    {
        $editais = $this->getDoctrine()
            ->getManager()
            ->getRepository(FrigaEdital::class)
            ->findAll();

        $pessoas = $this->getDoctrine()
            ->getManager()
            ->createQueryBuilder()
            ->select('fp')
            ->from(FrigaPessoa::class, 'fp');
        if (null !== $situacao) {
            if (0 == $situacao) {
                $pessoas->andWhere('fp.idSituacao = 0 or fp.idSituacao is null');
            } else {
                $pessoas->andWhere('fp.idSituacao = :idSituacao')
                    ->setParameter('idSituacao', $situacao);
            }
        }
        $pessoas = $pessoas->orderBy('fp.nome', 'asc')
            ->getQuery()->getResult();

        return $this->render('NteAplicacaoCadastrosBundle:frigarelatorio:inscricao.html.twig', [
            'frigaEdital' => $editais,
            'frigaPessoas' => $pessoas,
        ]);
    }

    public function andamentoAction(Request $request, FrigaEdital $edital = null)
    {
        $obj0 = new \stdClass();
        $obj0->qtdCandidatos = 0;
        $obj0->qtdHomologacaoAguardando = 0;
        $obj0->qtdHomologado = 0;
        $obj0->qtdHomologadoNao = 0;
        $obj0->qtdEntrevista = 0;
        $obj0->qtdClassificado = 0;
        $obj0->qtdDesClassificado = 0;
        $obj0->porcentagem = 0;
        $relatorio = [];

        return $this->render('@NteAplicacaoFriga/relatorio/andamento.html.twig', [
            'edital' => $edital,
            'relatorio' => $relatorio,
            'geral' => $obj0,
        ]);
    }
}
