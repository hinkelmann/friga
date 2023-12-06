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

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Nte\Admin\SuporteBundle\Entity\Suporte;
use Nte\Admin\SuporteBundle\Entity\SuporteIteracao;
use Nte\UsuarioBundle\Entity\Usuario;
use Smalot\PdfParser\Parser;

/**
 * FrigaArquivo.
 *
 * @ORM\Table(name="friga_arquivo")
 *
 * @ORM\Entity
 *
 * @ORM\HasLifecycleCallbacks()
 */
class FrigaArquivo
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
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", length=255, nullable=true)
     */
    private $tipo;

    /**
     * @var string
     *
     * @ORM\Column(name="contexto", type="string", length=255, nullable=true)
     */
    private $contexto;

    /**
     * @var string
     *
     * @ORM\Column(name="id_contexto", type="string", length=255, nullable=true)
     */
    private $idContexto;

    /**
     * @var string
     *
     * @ORM\Column(name="titulo", type="string", length=255, nullable=true)
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="nome", type="string", length=255, nullable=true)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="string", length=255, nullable=true)
     */
    private $hash;

    /**
     * @var string
     *
     * @ORM\Column(name="mime_type", type="string", length=255, nullable=true)
     */
    private $mimeType;

    /**
     * @var string
     *
     * @ORM\Column(name="atributos", type="string", length=255, nullable=true)
     */
    private $atributo;

    /**
     * @var string
     *
     * @ORM\Column(name="conteudo", type="text", nullable=true)
     */
    private $conteudo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="data_publicacao", type="datetime", nullable=true)
     */
    private $dataPublicacao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registro_data_atualizacao", type="datetime", nullable=true)
     */
    private $registroDataAtualizacao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registro_data_criacao", type="datetime", nullable=true)
     */
    private $registroDataCriacao;

    /**
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="Nte\UsuarioBundle\Entity\Usuario", inversedBy="idArquivo")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
     * })
     */
    private $idUsuario;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital", mappedBy="idArquivo")
     */
    private $idEdital;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="FrigaInscricaoPontuacao", mappedBy="idArquivo")
     */
    private $idPontuacao;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="FrigaInscricaoRecurso", mappedBy="idArquivo")
     */
    private $idInscricaoRecurso;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoProjetoParticipante", mappedBy="idArquivo")
     */
    private $idProjetoParticipante;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="FrigaInscricao", mappedBy="idArquivo")
     */
    private $idInscricao;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario", mappedBy="idArquivo")
     */
    private $idEditalUsuario;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuarioConvite", mappedBy="idArquivo")
     */
    private $idEditalUsuarioConvite;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Nte\Admin\SuporteBundle\Entity\SuporteIteracao", mappedBy="idArquivo")
     */
    private $idSuporteIteracao;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Nte\Admin\SuporteBundle\Entity\Suporte", mappedBy="idArquivo")
     */
    private $idSuporte;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->idEdital = new ArrayCollection();
        $this->idEditalUsuario = new ArrayCollection();
        $this->idEditalUsuarioConvite = new ArrayCollection();
        $this->idPontuacao = new ArrayCollection();
        $this->idInscricaoRecurso = new ArrayCollection();
        $this->idInscricao = new ArrayCollection();
        $this->idSuporteIteracao = new ArrayCollection();
        $this->idSuporte = new ArrayCollection();
        $this->idProjetoParticipante = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tipo.
     *
     * @param string $tipo
     *
     * @return FrigaArquivo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo.
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set contexto.
     *
     * @param string $contexto
     *
     * @return FrigaArquivo
     */
    public function setContexto($contexto)
    {
        $this->contexto = $contexto;

        return $this;
    }

    /**
     * Get contexto.
     *
     * @return string
     */
    public function getContexto()
    {
        return $this->contexto;
    }

    /**
     * Set titulo.
     *
     * @param string $titulo
     *
     * @return FrigaArquivo
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo.
     *
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set nome.
     *
     * @param string $nome
     *
     * @return FrigaArquivo
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get nome.
     *
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set path.
     *
     * @param string $path
     *
     * @return FrigaArquivo
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set hash.
     *
     * @param string $hash
     *
     * @return FrigaArquivo
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash.
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set MimeType.
     *
     * @param string $mimeType
     *
     * @return FrigaArquivo
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Get MimeType.
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @return string
     */
    public function getAtributo()
    {
        return $this->atributo;
    }

    /**
     * @param string $atributo
     *
     * @return FrigaArquivo
     */
    public function setAtributo($atributo)
    {
        $this->atributo = $atributo;

        return $this;
    }

    /**
     * @return string
     */
    public function getConteudo()
    {
        return $this->conteudo;
    }

    /**
     * @param string $conteudo
     *
     * @return FrigaArquivo
     */
    public function setConteudo($conteudo)
    {
        $this->conteudo = $conteudo;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDataPublicacao()
    {
        return $this->dataPublicacao;
    }

    /**
     * @param \DateTime $dataPublicacao
     *
     * @return FrigaArquivo
     */
    public function setDataPublicacao($dataPublicacao)
    {
        $this->dataPublicacao = $dataPublicacao;

        return $this;
    }

    /**
     * Set registroDataAtualizacao.
     *
     * @param \DateTime $registroDataAtualizacao
     *
     * @return FrigaArquivo
     */
    public function setRegistroDataAtualizacao($registroDataAtualizacao)
    {
        $this->registroDataAtualizacao = $registroDataAtualizacao;

        return $this;
    }

    /**
     * Get registroDataAtualizacao.
     *
     * @return \DateTime
     */
    public function getRegistroDataAtualizacao()
    {
        return $this->registroDataAtualizacao;
    }

    /**
     * Set registroDataCriacao.
     *
     * @param \DateTime $registroDataCriacao
     *
     * @return FrigaArquivo
     */
    public function setRegistroDataCriacao($registroDataCriacao)
    {
        $this->registroDataCriacao = $registroDataCriacao;

        return $this;
    }

    /**
     * Get registroDataCriacao.
     *
     * @return \DateTime
     */
    public function getRegistroDataCriacao()
    {
        return $this->registroDataCriacao;
    }

    /**
     * Set idUsuario.
     *
     * @return FrigaArquivo
     */
    public function setIdUsuario(Usuario $idUsuario = null)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * Get idUsuario.
     *
     * @return Usuario
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /**
     * Add idEdital.
     *
     * @return FrigaArquivo
     */
    public function addIdEdital(FrigaEdital $idEdital)
    {
        $this->idEdital[] = $idEdital;

        return $this;
    }

    /**
     * Remove idEdital.
     */
    public function removeIdEdital(FrigaEdital $idEdital)
    {
        $this->idEdital->removeElement($idEdital);
    }

    /**
     * Get idEdital.
     *
     * @return ArrayCollection
     */
    public function getIdEdital()
    {
        return $this->idEdital;
    }

    /**
     * Add idEdital.
     *
     * @return FrigaArquivo
     */
    public function addIdEditalUsuario(FrigaEditalUsuario $idEditalUsuario)
    {
        if (!$this->idEditalUsuario->contains($idEditalUsuario)) {
            $this->idEditalUsuario->add($idEditalUsuario);
        }

        return $this;
    }

    /**
     * Remove idEditalUsuario.
     */
    public function removeIdEditalUsuario(FrigaEditalUsuario $idEditalUsuario)
    {
        if ($this->idEditalUsuario->contains($idEditalUsuario)) {
            $this->idEditalUsuario->removeElement($idEditalUsuario);
        }
    }

    /**
     * Get idEditalUsuario.
     *
     * @return ArrayCollection
     */
    public function getIdEditalUsuario()
    {
        return $this->idEditalUsuario;
    }

    /**
     * Add idEdital.
     *
     * @return FrigaArquivo
     */
    public function addIdEditalUsuarioConvite(FrigaEditalUsuarioConvite $idEditalUsuarioConvite)
    {
        if (!$this->idEditalUsuarioConvite->contains($idEditalUsuarioConvite)) {
            $this->idEditalUsuarioConvite->add($idEditalUsuarioConvite);
        }

        return $this;
    }

    /**
     * Remove idEditalUsuario.
     *
     * @param FrigaEditalUsuarioConvite $idEditalUsuarioConvite
     */
    public function removeIdEditalUsuarioConvite(FrigaEditalUsuario $idEditalUsuarioConvite)
    {
        if ($this->idEditalUsuarioConvite->contains($idEditalUsuarioConvite)) {
            $this->idEditalUsuarioConvite->removeElement($idEditalUsuarioConvite);
        }
    }

    /**
     * Get idEditalUsuario.
     *
     * @return ArrayCollection
     */
    public function getIdEditalUsuarioConvite()
    {
        return $this->idEditalUsuarioConvite;
    }

    /**
     * Add idPontuacao.
     *
     * @return FrigaArquivo
     */
    public function addIdPontuacao(FrigaInscricaoPontuacao $idPontuacao)
    {
        $this->idPontuacao[] = $idPontuacao;

        return $this;
    }

    /**
     * Remove idPontuacao.
     */
    public function removeIdPontuacao(FrigaInscricaoPontuacao $idPontuacao)
    {
        $this->idPontuacao->removeElement($idPontuacao);
    }

    /**
     * Get idPontuacao.
     *
     * @return ArrayCollection
     */
    public function getIdPontuacao()
    {
        return $this->idPontuacao;
    }

    /**
     * Add idInscricaoRecurso.
     *
     * @return FrigaArquivo
     */
    public function addIdInscricaoRecurso(FrigaInscricaoRecurso $idInscricaoRecurso)
    {
        $this->idInscricaoRecurso[] = $idInscricaoRecurso;

        return $this;
    }

    /**
     * Remove idInscricaoRecurso.
     */
    public function removeIdInscricaoRecurso(FrigaInscricaoRecurso $idInscricaoRecurso)
    {
        $this->idInscricaoRecurso->removeElement($idInscricaoRecurso);
    }

    /**
     * Get idInscricaoRecurso.
     *
     * @return ArrayCollection
     */
    public function getIdInscricaoRecurso()
    {
        return $this->idInscricaoRecurso;
    }

    /**
     * Add idInscricao.
     *
     * @return FrigaArquivo
     */
    public function addIdInscricao(FrigaInscricao $idInscricao)
    {
        $this->idInscricao[] = $idInscricao;

        return $this;
    }

    /**
     * Remove idInscricao.
     */
    public function removeIdInscricao(FrigaInscricao $idInscricao)
    {
        $this->idInscricao->removeElement($idInscricao);
    }

    /**
     * Get idInscricao.
     *
     * @return ArrayCollection
     */
    public function getIdInscricao()
    {
        return $this->idInscricao;
    }

    /**
     * Add idSuporteIteracao.
     *
     * @return FrigaArquivo
     */
    public function addIdSuporteIteracao(SuporteIteracao $idSuporteIteracao)
    {
        $this->idSuporteIteracao[] = $idSuporteIteracao;

        return $this;
    }

    /**
     * Remove idSuporteIteracao.
     */
    public function removeIdSuporteIteracao(SuporteIteracao $idSuporteIteracao)
    {
        $this->idSuporteIteracao->removeElement($idSuporteIteracao);
    }

    /**
     * Get idSuporteIteracao.
     *
     * @return ArrayCollection
     */
    public function getIdSuporteIteracao()
    {
        return $this->idSuporteIteracao;
    }

    /**
     * Add idSuporte.
     *
     * @return FrigaArquivo
     */
    public function addIdSuporte(Suporte $idSuporte)
    {
        $this->idSuporte[] = $idSuporte;

        return $this;
    }

    /**
     * Remove idSuporte.
     */
    public function removeIdSuporte(Suporte $idSuporte)
    {
        $this->idSuporte->removeElement($idSuporte);
    }

    /**
     * Get idSuporte.
     *
     * @return ArrayCollection
     */
    public function getIdSuporte()
    {
        return $this->idSuporte;
    }

    /**
     * Add idProjetoParticipante.
     *
     * @return FrigaArquivo
     */
    public function addIdProjetoParticipante(FrigaInscricaoProjetoParticipante $idProjetoParticipante)
    {
        if (!$this->idProjetoParticipante->contains($idProjetoParticipante)) {
            $this->idProjetoParticipante->add($idProjetoParticipante);
        }

        return $this;
    }

    /**
     * Remove idProjetoParticipante.
     */
    public function removeIdProjetoParticipante(FrigaInscricaoProjetoParticipante $idProjetoParticipante)
    {
        if ($this->idProjetoParticipante->contains($idProjetoParticipante)) {
            $this->idProjetoParticipante->removeElement($idProjetoParticipante);
        }
    }

    /**
     * Get idProjetoParticipante.
     *
     * @return ArrayCollection
     */
    public function getIdProjetoParticipante()
    {
        return $this->idProjetoParticipante;
    }

    /**
     * @return string
     */
    public function getIdContexto()
    {
        return $this->idContexto;
    }

    /**
     * @param string $idContexto
     *
     * @return FrigaArquivo
     */
    public function setIdContexto($idContexto)
    {
        $this->idContexto = $idContexto;

        return $this;
    }

    /**
     * @ORM\PreUpdate
     *
     * @throws \Exception
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        if ($args->hasChangedField('registroDataCriacao')) {
            $this->setRegistroDataCriacao($args->getOldValue('registroDataCriacao'));
            $this->setRegistroDataAtualizacao(new \DateTime());
        }
    }

    /**
     * @ORM\PrePersist
     */
    public function PrePersist()
    {
        $this->setRegistroDataCriacao(new \DateTime());
        $this->setRegistroDataAtualizacao(new \DateTime());
    }

    public function getPeriodoDivulgacao()
    {
        return (new \DateTime()) > $this->getDataPublicacao();
    }

    public function isTemp()
    {
        return (
            'EDITAL' != $this->contexto
            and 'PERFIL' != $this->contexto
            and 'CONVITE' != $this->contexto
            and 'EDITALUSUARIO' != $this->contexto
            and $this->idInscricao->isEmpty()
            and $this->idPontuacao->isEmpty()
            and $this->idInscricaoRecurso->isEmpty()
            and $this->idProjetoParticipante->isEmpty()) ? 1 : 0;
    }

    public function getDescricaoContexto()
    {
        $str = '';
        if ('EDITAL' == $this->contexto and !$this->idEdital->isEmpty()) {
            /** @var FrigaEdital $item */
            foreach ($this->idEdital as $item) {
                $str .= $item->getTitulo();
            }
        } elseif (!$this->isTemp()) {
            if ('DOCUMENTO' == $this->contexto) {
                /** @var FrigaInscricao $item */
                foreach ($this->idInscricao as $item) {
                    switch ($this->idContexto) {
                        case 0:
                            $str .= 'Documento de identificação';
                            break;
                        case 1:
                            $str .= 'Comprovante de residência';
                            break;
                        case 2:
                            $str .= 'Memorial descritivo';
                            break;
                        case 3:
                            $str .= 'Outros anexos';
                            break;
                        default:
                            $str .= 'Documento de identificação';
                    }
                }
            }
            if ('PONTUACAO' == $this->contexto or 'CATEGORIA' == $this->contexto) {
                /** @var FrigaInscricaoPontuacao $item */
                foreach ($this->idPontuacao as $item) {
                    $str .= $item->getIdEditalPontuacao()->getTitulo();
                }
            }
            if ('RECURSO' == $this->contexto) {
                /** @var FrigaInscricaoRecurso $item */
                foreach ($this->idInscricaoRecurso as $item) {
                    $str .= 'Recurso:' . $item->getIdEditalEtapa()->getDescricao();
                }
            }
            if ('INSCRICAOPROJETO' == $this->contexto) {
                /** @var FrigaInscricaoProjetoParticipante $item */
                foreach ($this->idProjetoParticipante as $item) {
                    $str .= 'Projeto: ' . $item->getIdInscricao()->getProjetoTitulo();
                }
            }
            if ('PERFIL' == $this->contexto) {
                $str = 'Imagem de Perfil do usuário';
            }
        } else {
            $str = 'ARQUIVO TEMPORÁRIO:: ' . $this->contexto;
        }

        return $str;
    }

    /**
     * @return false|mixed|null
     */
    public function getInscricaoContexto()
    {
        if ($this->isTemp()) {
            return null;
        }
        switch ($this->contexto) {
            case 'DOCUMENTO':
                return $this->getIdInscricao()->isEmpty() ? null : $this->getIdInscricao()->first();
            case 'PONTUACAO':
            case 'CATEGORIA':
                return $this->idPontuacao->isEmpty() ? null : $this->idPontuacao->first()->getIdInscricao();
            case 'RECURSO':
                return $this->idInscricaoRecurso->isEmpty() ? null : $this->idInscricaoRecurso->first()->getIdInscricao();
            case 'INSCRICAOPROJETO':
                return $this->idProjetoParticipante->isEmpty() ? null : $this->idProjetoParticipante->first()->getIdInscricao();
            default:
                return null;
        }
    }

    public function getMetaContexto()
    {
        if (\in_array($this->contexto, ['EDITAL', 'PERFIL'])) {
            return $this->contexto;
        }
        if ($this->isTemp()) {
            return 'TEMP';
        }
        /** @var FrigaInscricao|null $inscricao */
        $inscricao = $this->getInscricaoContexto();
        if (!\is_null($inscricao) and -999 != $inscricao->getIdSituacao()) {
            return $this->contexto;
        } else {
            return 'INVALIDADO';
        }
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public function getParseText($quebra = false)
    {
        $parser = new Parser();
        $pdf = $parser->parseFile('/media/frigadata/' . $this->nome);
        $txt = $pdf->getText();
        if ($quebra) {
            $txt = \str_replace("\n", ' ', $txt);
        }

        return $txt;
    }

    public function isParseTextTipo($tipo)
    {
        $str = [
            -1 => 'venho por meio desta declarar que assumo o compromisso de manter sigilo e confidencialidade em relação a todas as informações e documentos aos quais terei acesso enquanto atuo como membro da banca examinadora do edital', 0 => 'declaro que estou impedido(a) de participar como membro da banca examinadora do edital', 1 => 'declaro que não me enquadro em nenhuma das condições de impedimento ou suspeição para participar como membro da banca examinadora do edital',
        ];

        return \strpos($this->getParseText(true), $str[$tipo]);
    }

    /**
     * @return string
     */
    public function der2pem($der_data)
    {
        $pem = \chunk_split(\base64_encode($der_data), 64, "\n");
        $pem = "-----BEGIN CERTIFICATE-----\n" . $pem . "-----END CERTIFICATE-----\n";

        return $pem;
    }

    /**
     * @return array
     */
    public function extract_pkcs7_signatures()
    {
        $file = '/media/frigadata/' . $this->nome;
        if (!\file_exists($file)) {
            return [];
        }
        $pdf_contents = \file_get_contents($file);

        $regexp = '/ByteRange\ \[\s*(\d+) (\d+) (\d+)/';
        $result = [];
        \preg_match_all($regexp, $pdf_contents, $result);
        $signatures = [];
        if (isset($result[0])) {
            $signature_count = \count($result[0]);
            for ($s = 0; $s < $signature_count; ++$s) {
                $start = $result[2][$s];
                $end = $result[3][$s];
                $signature = null;
                if ($stream = \fopen($file, 'rb')) {
                    $signature = \stream_get_contents($stream, $end - $start - 2, $start + 1);
                    \fclose($stream);
                    $signature = \hex2bin($signature);
                    $signatures[] = $signature;
                }
            }
        }

        return $signatures;
    }

    /**
     * @return array
     */
    public function extrairCertificado()
    {
        $parsed_certificates = [];
        foreach ($this->extract_pkcs7_signatures() as $pkcs7_der_signature) {
            $pkcs7_pem_signature = $this->der2pem($pkcs7_der_signature);
            $pem_certificates = [];
            $result = \openssl_pkcs7_read($pkcs7_pem_signature, $pem_certificates);
            if ($result) {
                foreach ($pem_certificates as $pem_certificate) {
                    $parsed_certificate = \openssl_x509_parse($pem_certificate);
                    $parsed_certificates[] = $parsed_certificate;
                }
            }
        }

        return $parsed_certificates;
    }

    /**
     * @return array
     */
    public function who_signed()
    {
        $signers = [];
        $pkcs7_der_signatures = $this->extract_pkcs7_signatures();
        if (!empty($pkcs7_der_signatures)) {
            $parsed_certificates = [];
            foreach ($pkcs7_der_signatures as $pkcs7_der_signature) {
                $pkcs7_pem_signature = $this->der2pem($pkcs7_der_signature);
                $pem_certificates = [];
                $result = \openssl_pkcs7_read($pkcs7_pem_signature, $pem_certificates);
                if ($result) {
                    foreach ($pem_certificates as $pem_certificate) {
                        $parsed_certificate = \openssl_x509_parse($pem_certificate);
                        $parsed_certificates[] = $parsed_certificate;
                    }
                }
            }

            // Remove certificate authorities certificates
            $people_certificates = [];
            foreach ($parsed_certificates as $certificate_a) {
                $is_authority = false;
                foreach ($parsed_certificates as $certificate_b) {
                    if ($certificate_a['subject'] == $certificate_b['issuer']) {
                        // If certificate A is of the issuer of certificate B, then
                        // certificate A belongs to a certificate authority and,
                        // therefore, should be ignored
                        $is_authority = true;
                        break;
                    }
                }
                if (!$is_authority) {
                    $people_certificates[] = $certificate_a;
                }
            }
            // Remove duplicate certificates
            $distinct_certificates = [];
            foreach ($people_certificates as $certificate_a) {
                $is_duplicated = false;
                if (\count($distinct_certificates) > 0) {
                    foreach ($distinct_certificates as $certificate_b) {
                        if (
                            ($certificate_a['subject'] == $certificate_b['subject'])
                            && ($certificate_a['serialNumber'] == $certificate_b['serialNumber'])
                            && ($certificate_a['issuer'] == $certificate_b['issuer'])
                        ) {
                            // If certificate B has the same subject, serial number
                            // and issuer as certificate A, then certificate B is a
                            // duplicate and, therefore, should be ignored
                            $is_duplicated = true;
                            break;
                        }
                    }
                }
                if (!$is_duplicated) {
                    $distinct_certificates[] = $certificate_a;
                }
            }
            foreach ($distinct_certificates as $certificate) {
                $signers[] = $certificate['subject']['CN'];
            }
        }

        return $signers;
    }
}
