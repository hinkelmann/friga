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

namespace Nte\SiteBundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCota;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacaoCategoria;
use Nte\SiteBundle\Model\Edital;
use Nte\SiteBundle\Model\File;
use Nte\SiteBundle\Model\Jobe;
use Nte\SiteBundle\Model\Liste;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ApiController extends AbstractFOSRestController
{
    public function __construct()
    {
        \opcache_invalidate(__FILE__, true);
    }

    /**
     * List public edital.
     *
     *  This call takes list all edital opened.
     *
     * @SWG\Response(
     *      response=200,
     *      description="Returns an collection of edital",
     *
     *     @SWG\Schema(
     *          type="array",
     *
     *          @SWG\Items(ref=@Model(type=Edital::class, groups={"public"}))
     *      )
     *  )
     *
     * @SWG\Parameter(
     *      name="order",
     *      in="query",
     *      type="string",
     *      description="The field used to order edital"
     *  )
     * @SWG\Parameter(
     *       name="category",
     *       in="query",
     *       type="string",
     *       description="The field used to filter  edital category"
     *   )
     * @SWG\Parameter(
     *        name="tags",
     *        in="query",
     *        type="string",
     *        description="The field used to filter edital tags"
     *    )
     * @SWG\Parameter(
     *         name="subscription",
     *         in="query",
     *         type="boolean",
     *         description="The field used to filter editail only subscriptions open"
     *     )
     *
     * @SWG\Tag(name="public")
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function getEditalAction(Request $request)
    {
        $subs = $request->query->get('subscription') ?? -1;
        if (-1 !== $subs) {
            dump(\boolval(\strtoupper($subs)));
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $editais = $em->createQueryBuilder()
            ->select('e')
            ->from(FrigaEdital::class, 'e')
            ->where('e.publico =3')
            ->getQuery()
            ->getResult();
        $tmp = [];
        /** @var FrigaEdital $item */
        foreach ($editais as $item) {
            if (-1 !== $subs) {
                if (('true' === $subs) != $item->getPeriodoInscricaoHabilitado()) {
                    continue;
                }
            }
            $obj = new \stdClass();
            $obj->uuid = $item->getUuid();
            $obj->title = $item->getTitulo();
            $obj->date = $item->getDataPublicacaoOficial() ? $item->getDataPublicacaoOficial()->format('Y-m-d H:i:s') : null;
            $obj->number = $item->getNumero();
            $obj->subscription = $item->getPeriodoInscricaoHabilitado();
            $obj->url = $this->generateUrl('nte_site_edital', [
                'id' => $item->getId(), 'url' => $item->getUrl(),
            ], UrlGeneratorInterface::ABSOLUTE_URL);
            $obj->about = $item->getSobre();
            $obj->job = $item->getInfo1();
            $obj->value = $item->getInfo2();
            $obj->contact = $item->getInfo3();
            $tmp[] = $obj;
        }

        return $this->json($tmp);
    }

    /**
     *  List public score scheme of an edital.
     *
     *   This call takes list all score scheme of an opened edital .
     *
     * @SWG\Response(
     *       response=200,
     *       description="Returns an collection of files",
     *
     *      @SWG\Schema(
     *           type="array",
     *
     *           @SWG\Items(ref=@Model(type=File::class, groups={"public"}))
     *       )
     *   )
     *
     * @SWG\Tag(name="public")
     *
     * @param string $uuid
     *
     * @return JsonResponse
     */
    public function getEditalScoreAction(Request $request, $uuid)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $edital = $em
            ->getRepository(FrigaEdital::class)
            ->findOneBy([
                'uuid' => $uuid,
                'publico' => 3,
            ]);

        $tmp = [];

        if (\is_null($edital)) {
            return $this->json([]);
        }
        /** @var FrigaEditalPontuacaoCategoria $item */
        foreach ($edital->getPontuacaoCategoriaPeso() as $item) {
            $obj0 = new \stdClass();
            $obj0->id = $item->getId();
            $obj0->parent = null;
            $obj0->type = 'item';
            $obj0->title = $item->getTitulo();
            $obj0->description = $item->getDescricao();
            $obj0->info0 = $item->getExplicacao();
            $obj0->info1 = $item->getExplicacaoTexto();
            $obj0->value = new \stdClass();
            $obj0->value->min = $item->getValorMinimo();
            $obj0->value->max = $item->getValorMaximo();
            $obj0->value->step = null;
            $obj0->value->description = $item->getValorTexto();
            $obj0->value->chronogram = [];
            $obj0->children = [];
            /** @var FrigaEditalPontuacaoCategoria $subitem */
            foreach ($item->getFilhos() as $subitem) {
                $obj1 = new \stdClass();
                $obj1->id = $subitem->getId();
                $obj1->parent = $item->getId();
                if ($subitem->isAgruparPontuacao()) {
                    $obj1->type = 'subitem';
                }
                $obj1->title = $subitem->getTitulo();
                $obj1->description = $subitem->getDescricao();
                $obj1->info0 = $subitem->getExplicacao();
                $obj1->info1 = $subitem->getExplicacaoTexto();
                $obj1->value = new \stdClass();
                $obj1->value->min = $subitem->getValorMinimo();
                $obj1->value->max = $subitem->getValorMaximo();
                $obj1->value->step = null;
                $obj1->value->description = $subitem->getValorTexto();
                $obj1->value->chronogram = [];

                $obj1->children = [];

                /** @var FrigaEditalPontuacao $subsubitem */
                foreach ($subitem->getPontuacao() as $subsubitem) {
                    $obj2 = new \stdClass();
                    $obj2->id = $subsubitem->getId();
                    $obj2->parent = $subitem->getId();
                    if ($subitem->isAgruparPontuacao()) {
                        $obj2->type = 'subitem';
                    } else {
                        $obj2->type = 'subsubitem';
                    }

                    $obj2->title = $subsubitem->getTitulo();
                    $obj2->description = $subsubitem->getDescricao();
                    $obj2->info0 = $subsubitem->getExplicacao();
                    $obj2->info1 = $subsubitem->getExplicacaoTexto();
                    $obj2->value = new \stdClass();
                    $obj2->value->min = $subitem->getValorMinimo();
                    $obj2->value->max = $subitem->getValorMaximo();
                    $obj2->value->step = $subsubitem->getValorIntervalo();
                    $obj2->value->description = $subitem->getValorTexto();
                    $obj2->value->chronogram = [];

                    /** @var FrigaEditalEtapa $subsubsubitem */
                    foreach ($subsubitem->getIdEtapa() as $subsubsubitem) {
                        $obj3 = new \stdClass();
                        $obj3->id = $subsubsubitem->getId();
                        $obj3->description = $subsubsubitem->getDescricao();
                        $obj3->final = \boolval($subsubsubitem->getFinal());
                        $obj3->type = new \stdClass();
                        $obj3->type->id = $subsubsubitem->getObjTipo()->id;
                        $obj3->type->description = $subsubsubitem->getObjTipo()->descricao;
                        $obj3->date = $subsubsubitem->getDataDivulgacao() ? $subsubsubitem->getDataDivulgacao()->format('Y-m-d H:i:s') : null;
                        $obj3->timestart = $subsubsubitem->getDataInicial() ? $subsubsubitem->getDataInicial()->format('Y-m-d H:i:s') : null;
                        $obj3->timeend = $subsubsubitem->getDataFinal() ? $subsubsubitem->getDataFinal()->format('Y-m-d H:i:s') : null;
                        $obj2->value->chronogram[] = $obj3;
                    }
                    $obj1->children[] = $obj2;
                }
                $obj0->children[] = $obj1;
            }
            $tmp[] = $obj0;
        }

        return $this->json($tmp);
    }

    /**
     *  List public files of an edital.
     *
     *   This call takes list all files of an opened edital .
     *
     * @SWG\Response(
     *       response=200,
     *       description="Returns an collection of files",
     *
     *      @SWG\Schema(
     *           type="array",
     *
     *           @SWG\Items(ref=@Model(type=File::class, groups={"public"}))
     *       )
     *   )
     *
     * @SWG\Tag(name="public")
     *
     * @param string $uuid
     *
     * @return JsonResponse
     */
    public function getFilesAction(Request $request, $uuid)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $files = $em->createQueryBuilder()
            ->select('a')
            ->from(FrigaArquivo::class, 'a')
            ->innerJoin('a.idEdital', 'e')
            ->where('e.publico =3')
            ->andWhere('e.uuid = :uuid')
            ->andWhere('a.dataPublicacao <= :dt0')
            ->setParameter('uuid', $uuid)
            ->setParameter('dt0', new \DateTime())
            ->getQuery()
            ->getResult();

        $tmp = [];
        /** @var FrigaArquivo $item */
        foreach ($files as $item) {
            $obj = new \stdClass();
            $obj->uuid = $item->getId();
            $obj->title = $item->getTitulo();
            $obj->mimeType = $item->getMimeType();
            $obj->date = $item->getDataPublicacao()->format('Y-m-d H:i:s');
            $obj->url = $this->generateUrl('nte_site_arquivo_download', ['id' => $item->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
            $tmp[] = $obj;
        }

        return $this->json($tmp);
    }

    /**
     * @param string $uuid
     *
     * @return JsonResponse
     */
    public function getChronogramAction(Request $request, $uuid)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $edital = $em
            ->getRepository(FrigaEdital::class)
            ->findOneBy([
                'uuid' => $uuid,
                'publico' => 3,
            ]);

        $tmp = [];
        foreach ($edital->getEtapaCronologica() as $item) {
            $obj3 = new \stdClass();
            $obj3->id = $item->getId();
            $obj3->description = $item->getDescricao();
            $obj3->final = \boolval($item->getFinal());
            $obj3->type = new \stdClass();
            $obj3->type->id = $item->getObjTipo()->id;
            $obj3->type->description = $item->getObjTipo()->descricao;
            $obj3->date = $item->getDataDivulgacao() ? $item->getDataDivulgacao()->format('Y-m-d H:i:s') : null;
            $obj3->timestart = $item->getDataInicial() ? $item->getDataInicial()->format('Y-m-d H:i:s') : null;
            $obj3->timeend = $item->getDataFinal() ? $item->getDataFinal()->format('Y-m-d H:i:s') : null;
            $tmp[] = $obj3;
        }

        return $this->json($tmp);
    }

    /**
     *  List public list of an edital.
     *
     *   This call takes list all Lists of an opened edital .
     *
     * @SWG\Response(
     *       response=200,
     *       description="Returns an collection of files",
     *
     *      @SWG\Schema(
     *           type="array",
     *
     *           @SWG\Items(ref=@Model(type=Liste::class, groups={"public"}))
     *       )
     *   )
     *
     * @SWG\Tag(name="public")
     *
     * @param string $uuid
     *
     * @return JsonResponse
     */
    public function getListsAction(Request $request, $uuid)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $lists = $em->createQueryBuilder()
            ->select('e')
            ->from(FrigaEdital::class, 'e')
            ->where('e.publico =3')
            ->andWhere('e.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->getQuery()
            ->getResult();

        $tmp = [];

        /** @var FrigaEditalCota $item */
        foreach ($lists as $item) {
            $obj = new \stdClass();
            $obj->uuid = $item->getId();
            $obj->title = $item->getDescricao();
            $tmp[] = $obj;
        }

        return $this->json($tmp);
    }

    /**
     *  List public jobs of an edital.
     *
     *   This call takes list all jobs of an opened edital .
     *
     * @SWG\Response(
     *       response=200,
     *       description="Returns an collection of files",
     *
     *      @SWG\Schema(
     *           type="array",
     *
     *           @SWG\Items(ref=@Model(type=Jobe::class, groups={"public"}))
     *       )
     *   )
     *
     * @SWG\Tag(name="public")
     *
     * @param string $uuid
     *
     * @return JsonResponse
     */
    public function getJobsAction(Request $request, $uuid)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $lists = $em->createQueryBuilder()
            ->select('e')
            ->from(FrigaEdital::class, 'e')
            ->where('e.publico =3')
            ->andWhere('e.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->getQuery()
            ->getResult();

        $tmp = [];

        /** @var FrigaEditalCota $item */
        foreach ($lists as $item) {
            $obj = new \stdClass();
            $obj->uuid = $item->getId();
            $obj->title = $item->getDescricao();
            $tmp[] = $obj;
        }

        return $this->json($tmp);
    }

    /**
     * @param string $uuid
     *
     * @return JsonResponse
     */
    public function getResultAction(Request $request, $uuid)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $etapa = $em->createQueryBuilder()
            ->select('a')
            ->from(FrigaArquivo::class, 'a')
            ->innerJoin('a.idEdital', 'e')
            ->where('e.publico =3')
            ->andWhere('e.uuid = :uuid')
            //    ->andWhere('a.dataPublicacao <= NOW()')
            //   ->setParameter('uuid', $uuid)
            ->getQuery()
            ->getResult();

        dump($etapa);
        exit;
        $tmp = [];

        return $this->json($tmp);
    }

    /**
     * @param string $uuid
     *
     * @return JsonResponse
     */
    public function getConvocationAction(Request $request, $uuid)
    {
        $tmp = [];

        return $this->json($tmp);
    }

    /**
     * @param string $uuid
     *
     * @return JsonResponse
     */
    public function getClassificationlistAction(Request $request, $uuid)
    {
        $tmp = [];

        return $this->json($tmp);
    }
}
