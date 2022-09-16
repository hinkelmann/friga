<?php
/**
 * Controlador padrão
 */

namespace Nte\SiteBundle\Controller;

use Curl\Curl;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\QueryBuilder;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaClassificacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaConvocacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCargo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCota;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao;
use Nte\UsuarioBundle\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use stdClass;
use DateTime;
use Exception;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class DefaultController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('NteSiteBundle:Default:index.html.twig', [

        ]);
    }

    /**
     * @return Response
     */
    public function loginViaTokenAction(Request $request)
    {

        //Parametros
        $tokenid = $request->query->get('token');
        $app = $request->query->get('app');

        if(is_null($tokenid) or is_null($app)){
            return $this->redirectToRoute('site_homepage');
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        //Identificacao do usuario pelo token
        $usuario = $em->createQueryBuilder()
            ->select('u')
            ->from(Usuario::class,'u')
            ->where('u.token = :tokenid')
            ->andWhere('u.tokenExpire > :dt0')
            ->setParameter('tokenid',$tokenid)
            ->setParameter('dt0', new DateTime())
            ->getQuery()->getSingleResult();

        if(is_null($usuario)){
            return $this->redirectToRoute('site_homepage');
        }

        //Autenticação
        $token = new UsernamePasswordToken($usuario, null, 'main', $usuario->getRoles());
        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('_security_main', serialize($token));
        $this->get("event_dispatcher")
            ->dispatch("security.interactive_login", new InteractiveLoginEvent($request, $token));

        //Reset do token
        $usuario->setToken(null)->setTokenExpire(null);
        $em->persist($usuario);
        $em->flush();

        //Redirecionamento
        switch ($app){
            case "XXXX":
                return $this->redirectToRoute('site_homepage_xxxx');
            case "YYYY":
                return $this->redirectToRoute('site_homepage_yyy');
            default:
                return $this->redirectToRoute('site_homepage');
        }
    }


    public function indexRedirectAction(Request $request)
    {
        return $this->redirectToRoute('nte_site_homepage');
    }

    /**
     * @param Request $request
     * @param FrigaEdital $edital
     * @return Response
     */
    public function editalAction(Request $request, FrigaEdital $edital)
    {
        if (($edital->getPublico() == 1 && $edital->getPeriodoInscricao()->getAndamentoPrazo() > 0)
            or ($edital->getPublico() == 2 && $edital->getPeriodoInscricao()->getAndamentoPrazo() < 100)
            or ($edital->getPublico() == 3)
            or ($this->isGranted('ROLE_ADMIN'))
        ) {
            return $this->render('NteSiteBundle:Default:edital.html.twig', [
                'edital' => $edital,
                //'info' => $this->gerarInfo($edital)
            ]);
        } else {
            return $this->redirectToRoute('nte_site_homepage');
        }
    }

    /**
     * @param FrigaEdital $edital
     * @return ArrayCollection
     */
    public function gerarInfo(FrigaEdital $edital)
    {
        /**
         *                     {% for etapa in edital.etapaCronologica|reverse  if etapa.periodoDivulgacao
         * and ( etapa.tipo == 4 or etapa.tipo == 5  or etapa.tipo == 7 ) %}
         * {% if etapa.tipo == 4 %}
         * <li>
         * <a href="{{ path('nte_site_edital_classificao', {'etapa': etapa.id, 'uuid': edital.uuid}) }}">
         * <h4><i class="fa fa-globe"></i></h4>
         * <strong> {{ etapa.dataDivulgacao|date('d/m/Y') }} </strong> -
         * {{ etapa.descricao }}
         * </a>
         * </li>
         * {% endif %}
         * {% if etapa.tipo == 5 %}
         * {% if etapa.final  == 1%}
         * {% if etapa.convocacaoData.count %}
         * {% for chave, valor in etapa.convocacaoData %}
         * <li>
         * <a href="{{ path('nte_site_edital_convocacao', {'etapa': etapa.id, 'uuid': edital.uuid, data: chave}) }}">
         * <h4><i class="fa fa-calendar-check-o"></i></h4>
         * {% if chave|date('Ymd')>  etapa.dataDivulgacao|date('Ymd') %}
         * <strong> {{ chave|date('d/m/Y') }}</strong>
         * {% else %}
         * <strong> {{ etapa.dataDivulgacao|date('d/m/Y') }} </strong> -
         * {% endif %}
         * {{ etapa.descricao }}
         * </a>
         * </li>
         * {% endfor %}
         * {% endif %}
         * {% else %}
         * <li>
         * <a href="{{ path('nte_site_edital_convocacao', {'etapa': etapa.id, 'uuid': edital.uuid}) }}">
         * <h4><i class="fa fa-calendar-check-o"></i></h4>
         * <strong> {{ etapa.dataDivulgacao|date('d/m/Y') }} </strong> -
         * {{ etapa.descricao }}
         * </a>
         * </li>
         * {% endif %}
         * {% endif %}
         * {% if etapa.tipo == 7 and etapa.idEtapa and etapa.idEtapa.recurso.count %}
         * <li>
         * <a href="{{ path('nte_site_edital_recurso', {'etapa': etapa.id, 'uuid': edital.uuid}) }}">
         * <h4><i class="fa fa-gavel"></i></h4>
         * <strong> {{ etapa.dataDivulgacao|date('d/m/Y') }} </strong> -
         * {{ etapa.descricao }}
         * </a>
         * </li>
         *
         * {% endif %}
         * {% endfor %}
         */
        $etapas = $edital->getEtapa()->filter(function (FrigaEditalEtapa $etapa) {
            if (in_array($etapa->getTipo(), [4, 5, 7])
                and $etapa->getPeriodoDivulgacao()
            ) return $etapa;
        });

        $arquivos = $edital->getIdArquivo()->filter(function (FrigaArquivo $arquivo) {
            return $arquivo->getPeriodoDivulgacao();
        });

        $tmp = [];
        /** @var FrigaEditalEtapa $etapa */
        foreach ($etapas as $etapa) {
            if ($etapa->getTipo() == 7 and $etapa->getIdEtapa() and $etapa->getIdEtapa()->getRecursos()->count())
                $obj = new stdClass();
            $obj->icone;
            $obj->titulo = $etapa->getDescricao();
        }

        /** @var FrigaArquivo $arquivo */
        foreach ($arquivos as $arquivo) {
            $obj = new stdClass();
            $obj->titulo = $arquivo->getTitulo();
            $obj->icone = "fa fa-file-pdf-o";
            $obj->data = $arquivo->getDataPublicacao();
            $obj->url = $this->generateUrl('nte_site_arquivo_download', ['id' => $arquivo->getId()]);
            $tmp[] = $obj;
        }

        //Ordenada por data
        uasort($tmp, function ($a, $b) {
            return $a->data <=> $b->data;
        });
        return new ArrayCollection($tmp);

    }

    public function recursoAction(Request $request, FrigaEdital $edital, FrigaEditalEtapa $etapa)
    {
        if (!$etapa->getPeriodoDivulgacao() or !$edital->getEtapa()->contains($etapa)) {
            return $this->redirectToRoute('nte_site_edital', [
                'id' => $edital->getId(), 'url' => $edital->getTitulo()
            ]);
        }
        $criteria = new Criteria();
        $criteria->orderBy(['idSituacao' => 'desc', 'registroDataCriacao' => 'asc']);

        $recursos = $etapa->getIdEtapa()->getRecurso()->matching($criteria);
        return $this->render('NteSiteBundle:Default:recurso.html.twig', [
            'etapa' => $etapa,
            'edital' => $edital,
            'recurso' => $recursos
        ]);
    }


    public function convocacaoAction(Request $request, FrigaEdital $edital, FrigaEditalEtapa $etapa)
    {
        if (!$etapa->getPeriodoDivulgacao() or !$edital->getEtapa()->contains($etapa)) {
            return $this->redirectToRoute('nte_site_edital', [
                'id' => $edital->getId(), 'url' => $edital->getTitulo()
            ]);
        }

        $data = $request->query->get('data');

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

        $convocacao = new ArrayCollection($convocacao);
        if ($data) {
            $convocacao = $convocacao->filter(function (FrigaConvocacao $c) use ($data) {
                return $c->getRegistroDataCriacao()->format('Y-m-d') == $data;
            });
        }

        if ($etapa->getFinal()) {
            $template = 'NteSiteBundle:Default:convocacao-final.html.twig';
        } else {
            $template = 'NteSiteBundle:Default:convocacao.html.twig';
        }
        return $this->render($template, [
            'etapa' => $etapa,
            'edital' => $edital,
            'convocacao' => $convocacao,
        ]);
    }

    public function listaAction(Request $request, FrigaEdital $edital, FrigaEditalEtapa $etapa)
    {

        if (!$this->isGranted('ROLE_ADMIN')) {
            if (!$etapa->getPeriodoDivulgacao() or !$edital->getEtapa()->contains($etapa)) {
                return $this->redirectToRoute('nte_site_edital', [
                    'id' => $edital->getId(), 'url' => $edital->getUuid()
                ]);
            }
        }

        $dados = [];

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        if ($etapa->getTipo() == 8) {
            $dados = $em
                ->createQueryBuilder()
                ->select('i')
                ->from(FrigaInscricao::class, 'i')
                ->where('i.idEdital = :edital')
                ->setParameter('edital', $edital->getId())
                ->andWhere('i.idSituacao > -999')
                ->addOrderBy('i.nome', 'asc')
                ->getQuery()->getResult();
        }
        if ($etapa->getTipo() == 9) {
            $dados = $em
                ->createQueryBuilder()
                ->select('feu')
                ->from(FrigaEditalUsuario::class, 'feu')
                ->innerJoin('feu.idUsuario', 'u')
                ->where('feu.idEdital = :edital')
                ->setParameter('edital', $edital->getId())
                ->addOrderBy('u.nome', 'asc')
                ->getQuery()->getResult();
        }
        $tmp = new ArrayCollection($dados);


        return $this->render('NteSiteBundle:Default:lista.html.twig', [
            'etapa' => $etapa,
            'edital' => $edital,
            'data' => $tmp,
        ]);
    }

    /**
     * @param Request $request
     * @param FrigaEdital $edital
     * @param FrigaEditalEtapa $etapa
     * @return Response
     * @throws \Exception
     */
    public function resultadoAction(Request $request, FrigaEdital $edital, FrigaEditalEtapa $etapa)
    {
        if (!$etapa->getPeriodoDivulgacao() or !$edital->getEtapa()->contains($etapa)) {
            return $this->redirectToRoute('nte_site_edital', [
                'id' => $edital->getId(), 'url' => $edital->getTitulo()
            ]);
        }

        $classificacao = new ArrayCollection();
        $geral = $etapa->getClassificacao()->getIterator();

        $geral->uasort(function (FrigaClassificacao $a, FrigaClassificacao $b) {
            return $a->getPosicao() <=> $b->getPosicao();
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
                            $obj->classificacao = $geral->filter(function (FrigaClassificacao $c) use ($cargo, $lista) {
                                return $c->getIdCargo()->getId() == $cargo->getId()
                                    and $c->getIdCota()->getId() == $lista->getId();
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
                    $obj->classificacao = $geral->filter(function (FrigaClassificacao $c) use ($cargo) {
                        return $c->getIdCargo()->getId() == $cargo->getId()
                            and $c->getIdCota() == null;
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
                $obj->classificacao = $geral->filter(function (FrigaClassificacao $c) use ($lista) {
                    return $c->getIdCota()->getId() == $lista->getId()
                        and $c->getIdCargo() == null;
                });
                $classificacao->add($obj);
            }
        }
        if ($edital->isResultado3()) {
            $obj = new stdClass();
            $obj->nome = "Classificação Geral";
            $obj->cargo = null;
            $obj->lista = null;
            $obj->classificacao = $geral->filter(function (FrigaClassificacao $c) {
                return $c->getIdCota() == null and $c->getIdCargo() == null;
            });
            $classificacao->add($obj);
        }
        return $this->render('NteSiteBundle:Default:classificacao.html.twig', [
            'etapa' => $etapa,
            'edital' => $edital,
            'classificacao' => $classificacao
        ]);
    }

    /**
     * @param Request $request
     * @param $edital
     * @param $inscricao
     * @return Response
     * @throws \Exception
     */
    public function resultadoIndividualAction(Request $request, $edital, $inscricao)
    {
        $em = $this->getDoctrine()->getManager();
        $edital = $em->getRepository(FrigaEdital::class)->findOneByUuid($edital);
        $inscricao = $em->getRepository(FrigaInscricao::class)->findOneByUuid($inscricao);

        if (!$inscricao or !$edital) {
            return $this->redirectToRoute('nte_site_homepage');
        }
        return $this->render('NteSiteBundle:Default:resultado-individual.html.twig', [
            'inscricao' => $inscricao,
            'edital' => $edital,
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function parteRodapeAction(Request $request)
    {
        return $this->render('NteSiteBundle:Default:rodape.html.twig', [

        ]);
    }

    /**
     * @param Request $request
     * @param integer $situacao
     * @return Response
     */
    public function parteEditaisAction(Request $request, $situacao)
    {
        $em = $this->getDoctrine()->getManager();
        $editais = $em->getRepository(FrigaEdital::class)
            ->findBy(['situacao' => $situacao], ['dataPublicacaoOficial' => 'desc']
            );
        $editais = new ArrayCollection($editais);
        $editaisComInscricao = new ArrayCollection();
        $editaisSemInscricao = new ArrayCollection();


        //Filtra os editais conforme a visibilidade
        $editais = $editais->filter(function (FrigaEdital $edital) {
            if (($edital->getPublico() == 1 && $edital->getPeriodoInscricao()->getAndamentoPrazo() > 0)
                or ($edital->getPublico() == 2 && $edital->getPeriodoInscricao()->getAndamentoPrazo() < 100)
                or ($edital->getPublico() == 3)
            ) {
                return $edital;
            }
        });

        //Se edital aberto, então separar em editais com inscrição aberta e em andamento
        if ($situacao == 1) {
            $editaisComInscricao = $editais->filter(function (FrigaEdital $edital) {
                return $edital->getPeriodoInscricaoHabilitado();
            });
            $editaisSemInscricao = $editais->filter(function (FrigaEdital $edital) {
                return !$edital->getPeriodoInscricaoHabilitado();
            });
        }

        return $this->render('NteSiteBundle:Default:bloco-editais.html.twig', [
            'editais' => $editais,
            'editaisComInscricao' => $editaisComInscricao,
            'editaisSemInscricao' => $editaisSemInscricao,
            'situacao' => $situacao
        ]);
    }

    //Token HEADER
    private $xhtk = "xx";

    //Token BODY
    private $xbtk = "yy";

    /**
     * @return \stdClass
     */
    public function getToken()
    {
        $csrftm = $this->container->get('security.csrf.token_manager');
        $obj = new stdClass();
        $obj->xhtk = $csrftm->refreshToken($this->xhtk)->getValue();
        $obj->xbtk = $csrftm->refreshToken($this->xbtk)->getValue();
        return $obj;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function checkToken(Request $request)
    {
        return ($this->isCsrfTokenValid($this->xhtk, $request->headers->get('xhtk'))
            and $this->isCsrfTokenValid($this->xbtk, $request->request->get('xbtk')));
    }

    public function testeAction(Request $request)
    {
        //$x = new Curl();
        $ls = file('https://ead12.proj.ufsm.br/client/fila.html');
        $arquivos = [];
        foreach ($ls as $x => $linha) {
            if ($x >= 1) {
                $y = str_replace(['.done', '</pre>'], "", substr($linha, 57, -1));
                $arquivos[] = $y;
            }
        }
        $servidor = [
            //'bbb001.nte.ufsm.br',
            '192.168.36.58',
            '192.168.36.59',
            '192.168.36.60',
            '192.168.36.61',
            '192.168.36.62',
            '192.168.36.63',
            '192.168.36.64',
        ];
        $lista = array_chunk($arquivos, 7);
        echo "#!/bin/bash\n";
        foreach ($lista as $conferencias) {
            foreach ($conferencias as $s => $conf) {
                echo "scp -CrpP2223 /media/bigbluebutton/recording/raw/$conf {$servidor[$s]}:/var/bigbluebutton/recording/raw/\n";
                echo "scp -CrpP2223 /media/bigbluebutton/recording/status/sanity/$conf.done {$servidor[$s]}:/var/bigbluebutton/recording/status/sanity/\n";
                echo "mv /media/bigbluebutton/recording/status/sanity/$conf.done  /media/slave/$s/\n";
                //dump($servidor[$s]."--".$conf);
            }


        }

        exit();

    }


    public function teste2Action(Request $request, $token)
    {

        return new JsonResponse([
            $this->isCsrfTokenValid('teste', $token),
            $request->isMethod('get'),
            $request->isMethod('post')
        ]);
    }
}
