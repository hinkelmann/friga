<?php

namespace Nte\Aplicacao\FrigaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;


use Doctrine\ORM\Mapping as ORM;

/**
 * Areas de conhecimento CAPES
 *
 * @ORM\Table(name="capes_area_conhecimento")
 * @ORM\Entity
 */
class CAPESAreaConhecimento
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
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

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return CAPESAreaConhecimento
     */
    public function setId(int $id): CAPESAreaConhecimento
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getArea(): ?string
    {
        return $this->area;
    }

    /**
     * @param string|null $area
     * @return CAPESAreaConhecimento
     */
    public function setArea(?string $area): CAPESAreaConhecimento
    {
        $this->area = $area;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAreaMestre(): ?string
    {
        return $this->areaMestre;
    }

    /**
     * @param string|null $areaMestre
     * @return CAPESAreaConhecimento
     */
    public function setAreaMestre(?string $areaMestre): CAPESAreaConhecimento
    {
        $this->areaMestre = $areaMestre;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getVersao(): ?string
    {
        return $this->versao;
    }

    /**
     * @param string|null $versao
     * @return CAPESAreaConhecimento
     */
    public function setVersao(?string $versao): CAPESAreaConhecimento
    {
        $this->versao = $versao;
        return $this;
    }




}