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

namespace Nte\Aplicacao\FrigaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Areas de conhecimento CAPES.
 *
 * @ORM\Table(name="capes_area_conhecimento")
 *
 * @ORM\Entity
 */
class CAPESAreaConhecimento
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     *
     * @ORM\Id
     *
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="area", type="string", length=255, nullable=true)
     */
    private $area;

    /**
     * @var string|null
     *
     * @ORM\Column(name="area_mestre", type="string", length=255, nullable=true)
     */
    private $areaMestre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="versao", type="string", length=255, nullable=true)
     */
    private $versao;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): CAPESAreaConhecimento
    {
        $this->id = $id;

        return $this;
    }

    public function getArea(): ?string
    {
        return $this->area;
    }

    public function setArea(?string $area): CAPESAreaConhecimento
    {
        $this->area = $area;

        return $this;
    }

    public function getAreaMestre(): ?string
    {
        return $this->areaMestre;
    }

    public function setAreaMestre(?string $areaMestre): CAPESAreaConhecimento
    {
        $this->areaMestre = $areaMestre;

        return $this;
    }

    public function getVersao(): ?string
    {
        return $this->versao;
    }

    public function setVersao(?string $versao): CAPESAreaConhecimento
    {
        $this->versao = $versao;

        return $this;
    }
}
