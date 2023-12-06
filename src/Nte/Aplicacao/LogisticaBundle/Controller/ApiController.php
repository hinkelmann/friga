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

namespace Nte\Aplicacao\LogisticaBundle\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nte\Aplicacao\LogisticaBundle\Entity\LogisticaBairro;
use Nte\Aplicacao\LogisticaBundle\Entity\LogisticaLocalidade;
use Nte\Aplicacao\LogisticaBundle\Entity\LogisticaLogradouro;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends AbstractFOSRestController
{
    public function getLogisticaCepAction(Request $request)
    {
        $cep = $request->query->get('cep');
        $endereco = $this->getDoctrine()->getRepository(LogisticaLogradouro::class)
            ->findOneBy(['cep' => $cep]);

        $view = $endereco ? $this->view($endereco) : $this->view(false);

        return $this->handleView($view);
    }

    public function getLogisticaLogradouroTermoAction(Request $request)
    {
        $termo = $request->attributes->get('termo');
        $localidade = $request->query->get('localidade') ?: '';
        $bairro = $request->query->get('bairro') ?: '';
        $uf = $request->query->get('uf') ?: '';
        $uf = $request->query->get('uf') ? " and localidade.estadoSigla = '$uf'" : '';

        $enderecos = $this->getDoctrine()->getManager()
            ->createQueryBuilder()
            ->select('logradouro')
            ->from(LogisticaLogradouro::class, 'logradouro')
            ->innerJoin(LogisticaLocalidade::class, 'localidade', 'WITH', 'logradouro.idCidade = localidade.id')
            ->innerJoin(LogisticaBairro::class, 'bairro', 'WITH', 'bairro.idCidade = localidade.id')
            ->where("logradouro.nome like '%$termo%'")
            ->andWhere("localidade.localidadeNome like '%$localidade%'" . $uf)
            ->andWhere("bairro.nome like '%$bairro%'")
            ->getQuery()
            ->getResult();

        return new JsonResponse($this->serializer->serialize($enderecos, 'json'));
    }

    public function getLogisticaLocalidadeTermoAction(Request $request)
    {
        $termo = $request->attributes->get('termo');

        $uf = $request->query->get('uf') ?: '';
        $uf = $request->query->get('uf') ? " and localidade.estadoSigla = '$uf'" : '';

        $enderecos = $this->getDoctrine()->getManager()
            ->createQueryBuilder()
            ->select('localidade')
            ->from(LogisticaLocalidade::class, 'localidade')
            ->where("localidade.localidadeNome like '%$termo%'" . $uf)
            ->getQuery()
            ->getResult();

        return new JsonResponse($this->serializer->serialize($enderecos, 'json'));
    }
}
