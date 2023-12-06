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

use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacaoCategoria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{
    public function getPontuacaoIndexAction(Request $request, FrigaEdital $edital)
    {
        $tmp = [];

        /** @var FrigaEditalPontuacaoCategoria $item */
        foreach ($edital->getPontuacaoCategoriaPeso() as $item) {
            $obj0 = new \stdClass();
            $obj0->id = $item->getId();
            // $obj0->parent = null;
            $obj0->text = $item->getDescricao();
            $obj0->state = new \stdClass();
            $obj0->state->opened = true;

            $obj0->data = [
                'min' => '',
                'nax' => '',
                'valor' => 'x' . ($item->getValorMaximo() + 0),
                'etapa' => [],
            ];
            $obj0->children = [];
            /** @var FrigaEditalPontuacaoCategoria $subitem */
            foreach ($item->getFilhos() as $subitem) {
                $etapas = [];

                $obj1 = new \stdClass();
                $obj1->id = $subitem->getId();
                $obj1->parent = $item->getId();
                $obj1->text = $subitem->getDescricao();
                $obj1->state = new \stdClass();
                $obj1->state->opened = true;
                $obj1->data = [
                    //'min'=>$subitem->getValorMinimo()+0,
                    'min' => '',
                    'max' => '',
                    //'max'=>$subitem->getValorMaximo()+0,
                    'valor' => $subitem->getValorMaximo() + 0,
                    'etapa' => [],
                ];

                if ($subitem->isAgruparPontuacao()) {
                    $obj1->type = 'item';
                }
                $obj1->children = [];
                foreach ($subitem->getPontuacao() as $subsubitem) {
                    $obj2 = new \stdClass();
                    $obj2->id = $subsubitem->getId();
                    $obj2->parent = $subitem->getId();
                    $obj2->text = $subsubitem->getDescricao();
                    $obj2->state = new \stdClass();
                    $obj2->state->opened = true;

                    $obj2->data = [
                        //'min'=>$subsubitem->getValorMinimo()+0,
                        'min' => '',
                        //'max'=>$subsubitem->getValorMaximo()+0,
                       // 'max' => "",
                        'max' => $subsubitem->getValorMaximo() + 0,
                    ];
                    if ($subitem->isAgruparPontuacao()) {
                        $obj2->type = 'iten';
                    } else {
                        $obj2->type = 'subitem';
                    }
                    $obj1->children[] = $obj2;
                }
                $obj0->children[] = $obj1;
            }
            $tmp[] = $obj0;
        }

        return $this->json($tmp);
    }
}
