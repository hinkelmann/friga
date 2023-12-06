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
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Nte\UsuarioBundle\Entity\Usuario;

/**
 * System Logs.
 *
 * @ORM\Table(name="log")
 *
 * @ORM\Entity
 *
 * @ORM\HasLifecycleCallbacks()
 */
class Log
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
     * @var Usuario|null
     *
     * @ORM\ManyToOne(targetEntity="Nte\UsuarioBundle\Entity\Usuario")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id", nullable=true)
     * })
     */
    private $idUsuario;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id_contexto", type="integer", nullable=true)
     */
    private $idContexto;

    /**
     * @var string|null
     *
     * @ORM\Column(name="contexto", type="string", length=255, nullable=true)
     */
    private $contexto;

    /**
     * @var string|null
     *
     * @ORM\Column(name="dominio", type="string", length=255, nullable=true)
     */
    private $dominio;

    /**
     * @var string|null
     *
     * @ORM\Column(name="metodo", type="string", length=255, nullable=true)
     */
    private $metodo;

    /**
     * @var int|null
     *
     * @ORM\Column(name="operacao", type="integer", nullable=true)
     */
    private $operacao;

    /**
     * @var int|null
     *
     * @ORM\Column(name="interface", type="integer", nullable=true)
     */
    private $interface;

    /**
     * @var string|null
     *
     * @ORM\Column(name="msg", type="text", nullable=true)
     */
    private $msg;

    /**
     * @var string|null
     *
     * @ORM\Column(name="uri", type="text", nullable=true)
     */
    private $uri;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registro_data_criacao", type="datetime", nullable=true)
     */
    private $registroDataCriacao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registro_data_atualizacao", type="datetime", nullable=true)
     */
    private $registroDataAtualizacao;

    private $aux;

    private $auxOperacao;

    private $auxUsuario;

    public function __construct()
    {
        //$this->registroDataAtualizacao = new DateTime();
        //$this->registroDataCriacao = new DateTime();
    }

    public function getStd()
    {
        $obj = new \stdClass();
        $obj->id = $this->id;
        $obj->idUsuario = new \stdClass();
        if (!\is_null($this->idUsuario)) {
            $obj->idUsuario->id = $this->idUsuario->getId();
            $obj->idUsuario->nome = $this->idUsuario->getNome();
        } else {
            $obj->idUsuario->id = 0;
            $obj->idUsuario->nome = 'INDEFINIDO';
            if (2 == $this->interface) {
                $obj->idUsuario->nome = 'EXTERNO';
            }
            if (0 == $this->interface) {
                $obj->idUsuario->nome = 'SISTEMA';
            }
        }
        $obj->idContexto = $this->idContexto;
        $obj->contexto = $this->contexto;
        //$obj->idContextoOrigem = $this->idContextoOrigem;
        //$obj->contextoOrigem = $this->contextoOrigem;
        $obj->dominio = $this->dominio;
        $obj->uri = $this->uri;
        if (!\is_null($this->uri)) {
            $this->uri = \str_replace(['/app.php/', '/app_dev.php'], '', $this->uri);
        }
        $obj->metodo = $this->metodo;
        $obj->operacao = $this->operacao;
        $obj->interface = $this->interface;
        $obj->msg = $this->msg;
        $obj->registroDataCriacao = $this->registroDataCriacao->format('d/m/Y H:i:s');
        $obj->registroDataAtualizacao = $this->registroDataAtualizacao->format('d/m/Y H:i:s');
        $obj->componente = 'WEB';
        $obj->interfaceDescricao = '';
        switch ($obj->interface) {
            case 0:
                $obj->interfaceDescricao = 'CRON';
                break;
            case 1:
                $obj->interfaceDescricao = 'USUÁRIO';
                break;
            case 2:
                $obj->interfaceDescricao = 'WEBHOOK';
                break;
        }

        return $obj;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Log
    {
        $this->id = $id;

        return $this;
    }

    public function getIdUsuario(): ?Usuario
    {
        return $this->idUsuario;
    }

    public function setIdUsuario(?Usuario $idUsuario): Log
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    public function getIdContexto(): ?int
    {
        return $this->idContexto;
    }

    public function setIdContexto(?int $idContexto): Log
    {
        $this->idContexto = $idContexto;

        return $this;
    }

    public function getContexto(): ?string
    {
        return $this->contexto;
    }

    public function setContexto(?string $contexto): Log
    {
        $this->contexto = $contexto;

        return $this;
    }

    public function getDominio(): ?string
    {
        return $this->dominio;
    }

    public function setDominio(?string $dominio): Log
    {
        $this->dominio = $dominio;

        return $this;
    }

    public function getMsg(): ?string
    {
        return $this->msg;
    }

    public function setMsg(?string $msg): Log
    {
        $this->msg = $msg;

        return $this;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function setUri(?string $uri): Log
    {
        $this->uri = $uri;

        return $this;
    }

    public function getMetodo(): ?string
    {
        return $this->metodo;
    }

    public function setMetodo(?string $metodo): Log
    {
        $this->metodo = $metodo;

        return $this;
    }

    public function getOperacao(): ?int
    {
        return $this->operacao;
    }

    public function setOperacao(?int $operacao): Log
    {
        $this->operacao = $operacao;

        return $this;
    }

    public function getInterface(): ?int
    {
        return $this->interface;
    }

    public function setInterface(?int $interface): Log
    {
        $this->interface = $interface;

        return $this;
    }

    public function getRegistroDataCriacao(): \DateTime
    {
        return $this->registroDataCriacao;
    }

    public function setRegistroDataCriacao(\DateTime $registroDataCriacao): Log
    {
        $this->registroDataCriacao = $registroDataCriacao;

        return $this;
    }

    public function getRegistroDataAtualizacao(): \DateTime
    {
        return $this->registroDataAtualizacao;
    }

    public function setRegistroDataAtualizacao(\DateTime $registroDataAtualizacao): Log
    {
        $this->registroDataAtualizacao = $registroDataAtualizacao;

        return $this;
    }

    /**
     * @ORM\PreUpdate
     *
     * @throws \Exception
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $this->registroDataAtualizacao = new \DateTime();
    }

    /**
     * @ORM\PrePersist
     *
     * @throws \Exception
     */
    public function PrePersist($args)
    {
        $this->registroDataCriacao = new \DateTime();
        $this->registroDataAtualizacao = new \DateTime();
    }

    public function getCtxAux($aux)
    {
        if (\is_null($aux)) {
            return null;
        }

        $uri = \str_replace(['/app.php/', '/app_dev.php'], '', $this->uri);
        $uriA = \explode('/', $uri);
        $ctx = new \stdClass();
        $ctx->msg = $uri;
        $ctx->usuario = null;

        $ctx->contexto = null;
        $ctx->contextoSecundario = null;

        $ctx->operacao = 'LEITURA';
        if ('POST' == $this->getMetodo()) {
            $ctx->operacao = 'GRAVAÇÃO';
        }

        // '/app/arquivo/visualizar/{id}'
        if (5 == \count($uriA) and 'app' == $uriA[1] and 'arquivo' == $uriA[2] and 'visualizar' == $uriA[3] and $uriA[4] > 0) {
            $ctx->contexto = 'ARQUIVO';
            $ctx->contextoSecundario = 'ARQUIVO';
            if (\array_key_exists($uriA[4], $aux->arquivo)) {
                /** @var FrigaArquivo $arquivo */
                $arquivo = $aux->arquivo[$uriA[4]];
                $ctx->usuario = $arquivo->getIdUsuario();
                $ctx->msg = "Visualização do arquivo '{$arquivo->getNome()}'";
                $ctx->contextoSecundario = $arquivo->getContexto();
                switch ($arquivo->getContexto()) {
                    case 'CATEGORIA':
                        /** @var FrigaEditalPontuacaoCategoria $pt */
                        $pt = $aux->categoria[$arquivo->getIdContexto()];
                        $ctx->msg .= ', relacionado ao comprovante de Pontuação: ' . $pt->getTitulo();
                        break;

                    case 'PONTUACAO':
                        /** @var FrigaEditalPontuacao $pt */
                        $pt = $aux->pontuacao[$arquivo->getIdContexto()];
                        $ctx->msg .= ', relacionado ao comprovante de Pontuação: ' . $pt->getTitulo();
                        break;

                    case 'RECURSO':
                        /** @var FrigaInscricaoRecurso $recurso */
                        $recurso = $aux->recurso[$arquivo->getIdContexto()];
                        $ctx->msg .= ', relacionado ao recurso: ' . $recurso->getUuid();
                        break;
                        break;
                    case 'DOCUMENTO':
                        switch ($arquivo->getIdContexto()) {
                            case null:
                            case 0:
                                $ctx->msg .= ', relacionado ao anexo documento de identificação';
                                break;
                            case 1:
                                $ctx->msg .= ', relacionado ao anexo do comprovante de residência';
                                break;
                            case 2:
                                $ctx->msg .= ', relacionado ao anexo do memorial descritivo';
                                break;
                            case 3:
                            case 4:
                                $ctx->msg .= ', relacionado a anexos de comprovantes';
                                break;
                        }
                        break;
                    case 'EDITAL':
                        $ctx->msg .= ', relacionado ao edital';
                        break;
                    case 'INSCRICAOPROJETO':
                        $ctx->msg .= ', relacionado ao projeto em anexo na inscrição';
                        break;
                    case 'PERFIL':
                        $ctx->msg .= ', relacionado a imagem de perfil ';
                        break;
                }
            } else {
                $ctx->msg = "Visualização do arquivo ID: $uriA[4]";
            }
        }

        //'/app/avaliacao/etapa/{etapa}'
        if (5 == \count($uriA) and 'app' == $uriA[1] and 'avaliacao' == $uriA[2] and 'etapa' == $uriA[3] and $uriA[4] > 0) {
            $ctx->contexto = 'AVALIAÇÃO';
            $ctx->contextoSecundario = 'LISTA-CANDIDATO';
            if (\array_key_exists($uriA[4], $aux->etapa)) {
                /** @var FrigaEditalEtapa $etapa */
                $etapa = $aux->etapa[$uriA[4]];
                $ctx->msg = 'Visualização da lista de candidatos para avaliação na etapa: ' . $etapa->getDescricao();
            } else {
                $ctx->msg = 'Visualização da lista de candidatos para avaliação na etapa ID: ' . $uriA[4];
            }
        }

        // '/app/avaliacao/etapa/{etapa}/inscricao/{uuid}'
        if (7 == \count($uriA) and 'app' == $uriA[1] and 'avaliacao' == $uriA[2]
            and 'etapa' == $uriA[3] and $uriA[4] > 0 and 'inscricao' == $uriA[5] and '' != $uriA[6]
        ) {
            $ctx->contexto = 'AVALIAÇÃO';
            $ctx->contextoSecundario = 'CANDIDATO';
            if (\array_key_exists($uriA[6], $aux->inscricao)) {
                /** @var FrigaInscricao $inscricao */
                $inscricao = $aux->inscricao[$uriA[6]];
                $ctx->usuario = $inscricao->getIdUsuario();
            }
            if (\array_key_exists($uriA[4], $aux->etapa)) {
                /** @var FrigaEditalEtapa $etapa */
                $etapa = $aux->etapa[$uriA[4]];
                if ('POST' == $this->getMetodo()) {
                    $ctx->msg = "Realização da avaliação do candidato: {$uriA[6]} na etapa: " . $etapa->getDescricao();
                } else {
                    $ctx->msg = "Visualização do formulário de avaliação do candidato: {$uriA[6]} na etapa: " . $etapa->getDescricao();
                }
            } else {
                if ('POST' == $this->getMetodo()) {
                    $ctx->msg = "Realização da avaliação do candidato: {$uriA[6]} na etapa ID: " . $uriA[4];
                } else {
                    $ctx->msg = "Visualização do formulário de  avaliação do candidato: {$uriA[6]} na etapa ID: " . $uriA[4];
                }
            }
        }

        // '/app/avaliacao/etapa/{etapa}/exportar/csv'
        if (7 == \count($uriA) and 'app' == $uriA[1] and 'avaliacao' == $uriA[2]
            and 'etapa' == $uriA[3] and $uriA[4] > 0 and 'exportar' == $uriA[5] and 'csv' == $uriA[6]
        ) {
            $ctx->contexto = 'AVALIAÇÃO';
            $ctx->contextoSecundario = 'EXPORTAR-CSV';
            if (\array_key_exists($uriA[4], $aux->etapa)) {
                /** @var FrigaEditalEtapa $etapa */
                $etapa = $aux->etapa[$uriA[4]];
                $ctx->msg = 'Realizou download de um arquivo CSV com os candidatos para avaliação na etapa: ' . $etapa->getDescricao();
            } else {
                $ctx->msg = 'Realizou download de um arquivo CSV com os candidatos para avaliação ID: ' . $uri[4];
            }
        }

        //'/app/convocacao/etapa/{etapa}'
        if (5 == \count($uriA) and 'app' == $uriA[1] and 'convocacao' == $uriA[2] and 'etapa' == $uriA[3] and $uriA[4] > 0) {
            $ctx->contexto = 'CONVOCAÇÃO';
            $ctx->contextoSecundario = 'LISTA-CANDIDATO';
            if (\array_key_exists($uriA[4], $aux->etapa)) {
                /** @var FrigaEditalEtapa $etapa */
                $etapa = $aux->etapa[$uriA[4]];
                $ctx->msg = 'Visualização da lista de candidatos aptos para convocação na etapa: ' . $etapa->getDescricao();
            } else {
                $ctx->msg = 'Visualização da lista de candidatos aptos para convocação na etapa ID: ' . $uriA[4];
            }
        }

        // '/app/convocacao/etapa/{etapa}/inscricao/{uuid}'
        if (7 == \count($uriA) and 'app' == $uriA[1] and 'convocacao' == $uriA[2]
            and 'etapa' == $uriA[3] and $uriA[4] > 0 and 'inscricao' == $uriA[5] and '' != $uriA[6]
        ) {
            $ctx->contexto = 'CONVOCAÇÃO';
            $ctx->contextoSecundario = 'CANDIDATO';
            if (\array_key_exists($uriA[6], $aux->inscricao)) {
                /** @var FrigaInscricao $inscricao */
                $inscricao = $aux->inscricao[$uriA[6]];
                $ctx->usuario = $inscricao->getIdUsuario();
            }
            if (\array_key_exists($uriA[4], $aux->etapa)) {
                /** @var FrigaEditalEtapa $etapa */
                $etapa = $aux->etapa[$uriA[4]];
                if ('POST' == $this->getMetodo()) {
                    $ctx->msg = "Realização da convocação do candidato: {$uriA[6]} na etapa: " . $etapa->getDescricao();
                } else {
                    $ctx->msg = "Visualização do formulário de convocação do candidato: {$uriA[6]} na etapa: " . $etapa->getDescricao();
                }
            } else {
                if ('POST' == $this->getMetodo()) {
                    $ctx->msg = "Realização da convocação do candidato: {$uriA[6]} na etapa ID: " . $uriA[4];
                } else {
                    $ctx->msg = "Visualização do formulário de  convocação do candidato: {$uriA[6]} na etapa ID: " . $uriA[4];
                }
            }
        }

        // '/app/convocacao/etapa/{etapa}/exportar/csv'
        if (7 == \count($uriA) and 'app' == $uriA[1] and 'convocacao' == $uriA[2]
            and 'etapa' == $uriA[3] and $uriA[4] > 0 and 'exportar' == $uriA[5] and 'csv' == $uriA[6]
        ) {
            $ctx->contexto = 'CONVOCAÇÃO';
            $ctx->contextoSecundario = 'EXPORTAR-CSV';
            if (\array_key_exists($uriA[4], $aux->etapa)) {
                /** @var FrigaEditalEtapa $etapa */
                $etapa = $aux->etapa[$uriA[4]];
                $ctx->msg = 'Realizou download de um arquivo CSV com os candidatos convocados na etapa: ' . $etapa->getDescricao();
            } else {
                $ctx->msg = 'Realizou download de um arquivo CSV com os candidatos convocados na etapa ID: ' . $uri[4];
            }
        }

        // '/app/convocacao/etapa/{etapa}/exportar/moodle'
        if (7 == \count($uriA) and 'app' == $uriA[1] and 'convocacao' == $uriA[2]
            and 'etapa' == $uriA[3] and $uriA[4] > 0 and 'exportar' == $uriA[5] and 'csv' == $uriA[6]
        ) {
            $ctx->contexto = 'CONVOCAÇÃO';
            $ctx->contextoSecundario = 'EXPORTAR-MOODLE';
            if (\array_key_exists($uriA[4], $aux->etapa)) {
                /** @var FrigaEditalEtapa $etapa */
                $etapa = $aux->etapa[$uriA[4]];
                $ctx->msg = 'Realizou a exportação para o moodle dos dados dos candidatos convocados na etapa: ' . $etapa->getDescricao();
            } else {
                $ctx->msg = 'Realizou a exportação para o moodle dos dados dos candidatos convocados na etapa ID: ' . $uri[4];
            }
        }
        //'/app/resultado/etapa/{etapa}/impressao'
        if (6 == \count($uriA) and 'app' == $uriA[1] and 'resultado' == $uriA[2]
            and 'etapa' == $uriA[3] and $uriA[4] > 0 and 'impressao' == $uriA[5]
        ) {
            $ctx->contexto = 'RESULTADO';
            $ctx->contextoSecundario = 'IMPRESSÃO';
            if (\array_key_exists($uriA[4], $aux->etapa)) {
                /** @var FrigaEditalEtapa $etapa */
                $etapa = $aux->etapa[$uriA[4]];
                $ctx->msg = 'Visualização da página de impressão  da convocação na etapa: ' . $etapa->getDescricao();
            } else {
                $ctx->msg = 'Visualização da página de impressão  da convocação na etapa etapa ID: ' . $uri[4];
            }
        }
        //'/app/convocacao/etapa/{etapa}/impressao/presenca'
        if (7 == \count($uriA) and 'app' == $uriA[1] and 'convocacao' == $uriA[2]
            and 'etapa' == $uriA[3] and $uriA[4] > 0 and 'impressao' == $uriA[5]
            and 'presenca' == $uriA[6]
        ) {
            $ctx->contexto = 'CONVOCAÇÃO';
            $ctx->contextoSecundario = 'IMPRESSÃO-PRESENÇA';
            if (\array_key_exists($uriA[4], $aux->etapa)) {
                /** @var FrigaEditalEtapa $etapa */
                $etapa = $aux->etapa[$uriA[4]];
                $ctx->msg = 'Visualização da página de impressão  com a lista de presença dos candidatos convocados na etapa: ' . $etapa->getDescricao();
            } else {
                $ctx->msg = 'Visualização da página de impressão  com a lista de presença dos candidatos convocados na etapa etapa ID: ' . $uri[4];
            }
        }
        //'/app/convocacao/etapa/{etapa}/impressao/relacao'
        if (7 == \count($uriA) and 'app' == $uriA[1] and 'convocacao' == $uriA[2]
            and 'etapa' == $uriA[3] and $uriA[4] > 0 and 'impressao' == $uriA[5]
            and 'relacao' == $uriA[6]
        ) {
            $ctx->contexto = 'CONVOCAÇÃO';
            $ctx->contextoSecundario = 'IMPRESSÃO';
            if (\array_key_exists($uriA[4], $aux->etapa)) {
                /** @var FrigaEditalEtapa $etapa */
                $etapa = $aux->etapa[$uriA[4]];
                $ctx->msg = 'Visualização da página de impressão com a listagem dos candidatos convocados na etapa: ' . $etapa->getDescricao();
            } else {
                $ctx->msg = 'Visualização da página de impressão com a listagem dos candidatos convocados na etapa etapa ID: ' . $uri[4];
            }
        }
        //'/app/convocacao/etapa/{etapa}/impressao/relacao-contato'
        if (7 == \count($uriA) and 'app' == $uriA[1] and 'convocacao' == $uriA[2]
            and 'etapa' == $uriA[3] and $uriA[4] > 0 and 'impressao' == $uriA[5]
            and 'relacao-contato' == $uriA[6]
        ) {
            $ctx->contexto = 'RESULTADO';
            $ctx->contextoSecundario = 'IMPRESSÃO-RELAÇÃO';
            if (\array_key_exists($uriA[4], $aux->etapa)) {
                /** @var FrigaEditalEtapa $etapa */
                $etapa = $aux->etapa[$uriA[4]];
                $ctx->msg = 'Visualização da página de impressão com a listagem dos contatos dos candidatos convocados na etapa: ' . $etapa->getDescricao();
            } else {
                $ctx->msg = 'Visualização da página de impressão com a listagem dos contatos dos candidatos convocados na etapa etapa ID: ' . $uri[4];
            }
        }
        //'/app/resultado/parcial/{etapa}'
        if (5 == \count($uriA) and 'app' == $uriA[1] and 'resultado' == $uriA[2] and 'parcial' == $uriA[3] and $uriA[4] > 0) {
            $ctx->contexto = 'RESULTADO';
            $ctx->contextoSecundario = 'LISTA-PARCIAL';
            if (\array_key_exists($uriA[4], $aux->etapa)) {
                /** @var FrigaEditalEtapa $etapa */
                $etapa = $aux->etapa[$uriA[4]];
                $ctx->msg = 'Visualização do resultado parcial na etapa: ' . $etapa->getDescricao();
            } else {
                $ctx->msg = 'Visualização do resultado parcial na etapa ID: ' . $uri[4];
            }
        }
        //'/app/resultado/etapa/{etapa}/'
        if (6 == \count($uriA) and 'app' == $uriA[1] and 'resultado' == $uriA[2]
            and 'etapa' == $uriA[3] and $uriA[4] > 0 and '' == $uriA[5]) {
            $ctx->contexto = 'RESULTADO';
            $ctx->contextoSecundario = 'LISTA-DEFINITIVA';
            if (\array_key_exists($uriA[4], $aux->etapa)) {
                /** @var FrigaEditalEtapa $etapa */
                $etapa = $aux->etapa[$uriA[4]];
                $ctx->msg = 'Visualizou o resultado definitivo para publicação da etapa: ' . $etapa->getDescricao();
            } else {
                $ctx->msg = 'Visualizou o resultado definitivo para publicação da etapa ID: ' . $uri[4];
            }
        }

        //'/app/resultado/etapa/{etapa}/confirmar-posicao'
        if (6 == \count($uriA) and 'app' == $uriA[1] and 'resultado' == $uriA[2]
            and 'etapa' == $uriA[3] and $uriA[4] > 0 and 'confirmar-posicao' == $uriA[5]) {
            $ctx->operacao = 'GRAVAÇÃO';
            $ctx->contexto = 'RESULTADO';
            $ctx->contextoSecundario = 'POSIÇÃO-EMPATE-CONFIRMAR';
            if (\array_key_exists($uriA[4], $aux->etapa)) {
                /** @var FrigaEditalEtapa $etapa */
                $etapa = $aux->etapa[$uriA[4]];
                $ctx->msg = 'Confirmação da posição do candidato em situação de empate no resultado definitivo da etapa: ' . $etapa->getDescricao();
            } else {
                $ctx->msg = 'Confirmação da posição do candidato em situação de empate no resultado da etapa ID: ' . $uri[4];
            }
        }

        //'/app/resultado/etapa/{etapa}/posicao/{uuid}'
        if (7 == \count($uriA) and 'app' == $uriA[1] and 'resultado' == $uriA[2]
            and 'etapa' == $uriA[3] and $uriA[4] > 0 and 'posicao' == $uriA[5]) {
            $ctx->operacao = 'GRAVAÇÃO';
            $ctx->contexto = 'RESULTADO';
            $ctx->contextoSecundario = 'POSIÇÃO-EMPATE-ALTERAR-';
            if (\array_key_exists($uriA[4], $aux->etapa)) {
                /** @var FrigaEditalEtapa $etapa */
                $etapa = $aux->etapa[$uriA[4]];
                $ctx->msg = 'Alteração da posição do candidato em situação de empate no resultado definitivo da etapa: ' . $etapa->getDescricao();
            } else {
                $ctx->msg = 'Alteração da posição do candidato em situação de empate no resultado da etapa ID: ' . $uri[4];
            }
        }

        //'/app/resultado/etapa/{etapa}/remover'
        if (6 == \count($uriA) and 'app' == $uriA[1] and 'resultado' == $uriA[2]
            and 'etapa' == $uriA[3] and $uriA[4] > 0 and 'remover' == $uriA[5]) {
            $ctx->operacao = 'GRAVAÇÃO';
            $ctx->contexto = 'RESULTADO';
            $ctx->contextoSecundario = 'EXCLUSÃO';
            if (\array_key_exists($uriA[4], $aux->etapa)) {
                /** @var FrigaEditalEtapa $etapa */
                $etapa = $aux->etapa[$uriA[4]];
                $ctx->msg = 'Exclusão dos resultados definitivos para publicação da etapa: ' . $etapa->getDescricao();
            } else {
                $ctx->msg = 'Exclusão dos resultados definitivos para publicação da etapa ID: ' . $uri[4];
            }
        }

        //'/app/resultado/etapa/{etapa}/gerar'
        if (6 == \count($uriA) and 'app' == $uriA[1] and 'resultado' == $uriA[2]
            and 'etapa' == $uriA[3] and $uriA[4] > 0 and 'gerar' == $uriA[5]) {
            $ctx->operacao = 'GRAVAÇÃO';
            $ctx->contexto = 'RESULTADO';
            $ctx->contextoSecundario = 'GERAÇÃO';
            if (\array_key_exists($uriA[4], $aux->etapa)) {
                /** @var FrigaEditalEtapa $etapa */
                $etapa = $aux->etapa[$uriA[4]];
                $ctx->msg = 'Geração dos resultados definitivos para publicação da etapa: ' . $etapa->getDescricao();
            } else {
                $ctx->msg = 'Geração os resultados definitivos para publicação da etapa ID: ' . $uri[4];
            }
        }

        //'/app/resultado/etapa/{etapa}/form'
        if (6 == \count($uriA) and 'app' == $uriA[1] and 'resultado' == $uriA[2]
            and 'etapa' == $uriA[3] and $uriA[4] > 0 and 'form' == $uriA[5]) {
            $ctx->contexto = 'RESULTADO';
            $ctx->contextoSecundario = 'COMPROVANTE';
            if (\array_key_exists($uriA[4], $aux->etapa)) {
                /** @var FrigaEditalEtapa $etapa */
                $etapa = $aux->etapa[$uriA[4]];
                $ctx->msg = 'Formulário de comprovante de resultado definitivos da etapa: ' . $etapa->getDescricao();
            } else {
                $ctx->msg = 'Formulário de comprovante de resultados definitivos da etapa ID: ' . $uri[4];
            }
        }

        //'/app/resultado/etapa/{etapa}/impressao'
        if (6 == \count($uriA) and 'app' == $uriA[1] and 'resultado' == $uriA[2]
            and 'etapa' == $uriA[3] and $uriA[4] > 0 and 'impressao' == $uriA[5]) {
            $ctx->contexto = 'RESULTADO';
            $ctx->contextoSecundario = 'IMPRESSÃO';
            if (\array_key_exists($uriA[4], $aux->etapa)) {
                /** @var FrigaEditalEtapa $etapa */
                $etapa = $aux->etapa[$uriA[4]];
                $ctx->msg = 'Visualização da página de impressão do resultado definitivo para publicação da etapa: ' . $etapa->getDescricao();
            } else {
                $ctx->msg = 'Visualização da página de impressão do resultado definitivo para publicação da etapa ID: ' . $uri[4];
            }
        }

        // '/app/resultado/etapa/{etapa}/exportar/csv'
        if (7 == \count($uriA) and 'app' == $uriA[1] and 'resultado' == $uriA[2]
            and 'etapa' == $uriA[3] and $uriA[4] > 0 and 'exportar' == $uriA[5] and 'csv' == $uriA[6]
        ) {
            $ctx->contexto = 'RESULTADO';
            $ctx->contextoSecundario = 'EXPORTAR-CSV';
            if (\array_key_exists($uriA[4], $aux->etapa)) {
                /** @var FrigaEditalEtapa $etapa */
                $etapa = $aux->etapa[$uriA[4]];
                $ctx->msg = 'Realização do download de um arquivo CSV com os candidatos classificados na etapa: ' . $etapa->getDescricao();
            } else {
                $ctx->msg = 'Realização do download de um arquivo CSV com os candidatos classificados na ID: ' . $uri[4];
            }
        }

        //'/app/relatorio/inscricao-perfil/{uuid}'
        if (5 == \count($uriA) and 'app' == $uriA[1] and 'relatorio' == $uriA[2] and 'inscricao-perfil' == $uriA[3] and '' != $uriA[4]) {
            $ctx->contexto = 'RELATÓRIO';
            $ctx->contextoSecundario = 'INSCRIÇÃO-DADOS';
            if (\array_key_exists($uriA[4], $aux->inscricao)) {
                /** @var FrigaInscricao $inscricao */
                $inscricao = $aux->inscricao[$uriA[4]];
                $ctx->usuario = $inscricao->getIdUsuario();
            }
            $ctx->msg = 'Visualização  dos dados e da pontuação da inscrição: ' . $uriA[4];
        }

        //'/app/relatorio/resumo/{uuid}'
        if (5 == \count($uriA) and 'app' == $uriA[1] and 'relatorio' == $uriA[2] and 'resumo' == $uriA[3] and '' != $uriA[4]) {
            $ctx->contexto = 'RELATÓRIO';
            $ctx->contextoSecundario = 'GERAL';
            $ctx->msg = 'Visualização do relatório geral do edital';
        }
        //'/app/relatorio/andamento/{uuid}'
        if (5 == \count($uriA) and 'app' == $uriA[1] and 'relatorio' == $uriA[2] and 'andamento' == $uriA[3] and '' != $uriA[4]) {
            $ctx->contexto = 'RELATÓRIO';
            $ctx->contextoSecundario = 'ANDAMENTO';
            $ctx->msg = 'Visualização do relatório de andamento do edital';
        }
        //'/app/relatorio/convocacao/{uuid}'
        if (5 == \count($uriA) and 'app' == $uriA[1] and 'relatorio' == $uriA[2] and 'convocacao' == $uriA[3] and '' != $uriA[4]) {
            $ctx->contexto = 'RELATÓRIO';
            $ctx->contextoSecundario = 'CONVOCAÇÃO';
            $ctx->msg = 'Visualização do relatório com a listagem de todos os convocados no edital';
        }
        //'/app/relatorio/recurso/{uuid}'
        if (5 == \count($uriA) and 'app' == $uriA[1] and 'relatorio' == $uriA[2] and 'recurso' == $uriA[3] and '' != $uriA[4]) {
            $ctx->contexto = 'RELATÓRIO';
            $ctx->contextoSecundario = 'RECURSOS';
            $ctx->msg = 'Visualização do relatório com a listagem de todos os recursos no edital';
        }
        //'/app/relatorio/inscritos/{uuid}'
        if (5 == \count($uriA) and 'app' == $uriA[1] and 'relatorio' == $uriA[2] and 'inscritos' == $uriA[3] and '' != $uriA[4]) {
            $ctx->contexto = 'RELATÓRIO';
            $ctx->contextoSecundario = 'INSCRIÇÕES';
            $ctx->msg = 'Visualização do relatório com a listagem de todos os inscritos no edital';
        }

        if (\count($uriA) > 3 and 'app' == $uriA[1] and 'edital' == $uriA[2]) {
            $ctx->contexto = 'CONFIGURAÇÃO';
            //'/app/edital/remover/{uuid}'
            if ('remover' == $uriA[3]) {
                $ctx->contextoSecundario = 'EXCLUSÃO-EDITAL';
                $ctx->operacao = 'GRAVAÇÃO';
                $ctx->msg = 'Exclusão do edital';
            }

            //'/app/edital/{uuid}/exportador'
            if ('exportador' == $uriA[4]) {
                $ctx->contextoSecundario = 'EXPORTAR';
                if ('POST' == $this->metodo) {
                    $ctx->msg = 'Realização do download de um arquivo com as configurações do edital';
                } else {
                    $ctx->msg = 'Visualização do formulário de exportação das configurações do edital';
                }
            }

            //'/app/edital/{uuid}/importar'
            if ('exportador' == $uriA[4]) {
                $ctx->contextoSecundario = 'IMPORTAR';
                if ('POST' == $this->metodo) {
                    $ctx->msg = 'Realização de upload de um arquivo  com as configurações do edital';
                } else {
                    $ctx->msg = 'Visualização do formulário de exportação das configurações do edital';
                }
            }

            //'/app/edital/{uuid}/exportar/csv/logs'
            if ('exportar' == $uriA[4] and 'csv' == $uriA[5] and 'logs' == $uriA[6]) {
                $ctx->operacao = 'GRAVAÇÃO';
                $ctx->contextoSecundario = 'EXPORTAR-LOG-CSV';
                $ctx->msg = 'Realização do download de um arquivo csv com os logs do edital';
            }

            //'/app/edital/{uuid}/arquivo'
            if ('arquivo' == $uriA[4]) {
                $ctx->contextoSecundario = 'ARQUIVO-PÚBLICO';
                $ctx->msg = 'Visualização dos arquivos do edital';
            }

            //'/app/edital/{uuid}/config'
            if ('config' == $uriA[4]) {
                $ctx->contextoSecundario = 'GERAL';
                if ('POST' == $this->metodo) {
                    $ctx->msg = 'Alteração das configurações gerais e site do edital';
                } else {
                    $ctx->msg = 'Visualização do formulário de configurações gerais e site do edital';
                }
            }

            //'/app/edital/{uuid}/termo'
            if ('termo' == $uriA[4]) {
                $ctx->contextoSecundario = 'TERMO';
                if ('POST' == $this->metodo) {
                    $ctx->msg = 'Alteração das configurações do termo do edital';
                } else {
                    $ctx->msg = 'Visualização do formulário de configurações do termo do edital';
                }
            }

            //'/app/edital/{uuid}/inscricoes'
            if ('inscricoes' == $uriA[4]) {
                $ctx->contextoSecundario = 'INSCRIÇÃO';
                if ('POST' == $this->metodo) {
                    $ctx->msg = 'Alteração das configurações do formulário de inscrição do edital';
                } else {
                    $ctx->msg = 'Visualização do formulário de configurações do formulário de inscrição do edital';
                }
            }

            //'/app/edital/{uuid}/resultados'
            if ('resultados' == $uriA[4]) {
                $ctx->contextoSecundario = 'RESULTADO';
                if ('POST' == $this->metodo) {
                    $ctx->msg = 'Alteração das configurações de apresentação dos resultados do edital';
                } else {
                    $ctx->msg = 'Visualização do formulário de configurações do formulário de apresentação dos resultados do edital';
                }
            }

            //'/app/edital/{uuid}/avaliador'
            if ('avaliador' == $uriA[4]) {
                $ctx->contextoSecundario = 'BANCA';
                if ('POST' == $this->metodo) {
                    $ctx->msg = 'Alteração das configurações da banca de avaliação do edital';
                } else {
                    $ctx->msg = 'Visualização das configurações da banca de avaliação do edital';
                }
            }

            //'/app/edital/{uuid}/etapa'
            if ('etapa' == $uriA[4]) {
                $ctx->contextoSecundario = 'CRONOGRAMA';
                if ('POST' == $this->metodo) {
                    $ctx->msg = 'Alteração das configurações do cronograma do edital';
                } else {
                    $ctx->msg = 'Visualização das configurações do cronograma do edital';
                }
            }

            //'/app/edital/{uuid}/pontuacao'
            if ('pontuacao' == $uriA[4]) {
                $ctx->contextoSecundario = 'PONTUAÇÃO';
                if ('POST' == $this->metodo) {
                    $ctx->msg = 'Alteração das configurações de pontuação do edital';
                } else {
                    $ctx->msg = 'Visualização das configurações de pontuação do edital';
                }
            }

            //'/app/edital/{uuid}/cargo'
            if ('cargo' == $uriA[4]) {
                $ctx->contextoSecundario = 'CARGO';
                if ('POST' == $this->metodo) {
                    $ctx->msg = 'Alteração das configurações de cargos do edital';
                } else {
                    $ctx->msg = 'Visualização das configurações de cargos do edital';
                }
            }

            //'/app/edital/{uuid}/cota'
            if ('cota' == $uriA[4]) {
                $ctx->contextoSecundario = 'LISTA';
                if ('POST' == $this->metodo) {
                    $ctx->msg = 'Alteração das configurações de listas de classificação do edital';
                } else {
                    $ctx->msg = 'Visualização das configurações de listas de classificação do edital';
                }
            }

            //'/app/edital/{uuid}/desempate/'
            if ('desempate' == $uriA[4]) {
                $ctx->contextoSecundario = 'DESEMPATE';
                if ('POST' == $this->metodo) {
                    $ctx->msg = 'Alteração das configurações dos critérios de desempate do edital';
                } else {
                    $ctx->msg = 'Visualização das configurações dos critérios de desempate do edital';
                }
            }

            //'/app/edital/{uuid}/logs/'
            if ('logs' == $uriA[4]) {
                $ctx->contextoSecundario = 'LOGS';
                $ctx->msg = 'Visualização dos logs do edital';
            }
        }
        if (\count($uriA) > 3 and 'candidato' == $uriA[1]) {
            $ctx->contexto = 'CANDIDATO';
            $ctx->usuario = $this->idUsuario;
            //    , '/candidato/inscricao-projeto/{uuid}'
            //'/candidato/cancelar/{uuid}'
            if ('cancelar' == $uriA[2]) {
                $ctx->operacao = 'GRAVAÇÃO';
                $ctx->contextoSecundario = 'INSCRIÇÃO-CANCELADA';
                $ctx->msg = 'Realizou o cancelamento da inscrição:' . $uriA[3];
            }

            //'/candidato/inscricacao-concluida/{uuid}'
            if ('inscricacao-concluida' == $uriA[2]) {
                $ctx->contextoSecundario = 'INSCRIÇÃO-CONFIRMAÇÃO';
                $ctx->msg = 'Visualização da mensagem de confirmação da inscrição:' . $uriA[3];
            }

            //'/candidato/inscricacao-realizada/{uuid}'
            if ('inscricacao-realizada' == $uriA[2]) {
                $ctx->contextoSecundario = 'INSCRIÇÃO-VISUALIZAÇÃO';
                $ctx->msg = 'Visualização dos dados da inscrição:' . $uriA[3];
            }

            //'/candidato/inscricacao/{uuid}'
            if ('inscricacao' == $uriA[2]) {
                $ctx->contextoSecundario = 'INSCRIÇÃO-FORMULÁRIO';
                if ('POST' == $this->metodo) {
                    $ctx->msg = 'Realização da inscrição do edital';
                } else {
                    $ctx->msg = 'Visualização do formulário de inscrição do edital';
                }
            }

            //'/candidato/recursos/{uuid}/ver/'
            if ('recursos' == $uriA[2] and 'ver' == $uriA[4]) {
                $ctx->contextoSecundario = 'RECURSO';
                if (\array_key_exists($uriA[3], $aux->recurso)) {
                    /** @var FrigaInscricaoRecurso $recurso */
                    $recurso = $aux->recurso[$uriA[3]];
                    $ctx->msg = "Visualização do recurso:{$uriA[3]} submetido na etapa: " . $recurso->getIdEditalEtapa()->getDescricao();
                } else {
                    $ctx->msg = 'Visualização do recurso submetido na etapa: ';
                }
            }

            // '/candidato/recursos/{etapa}/formulario/{uuid}'
            if ('recursos' == $uriA[2] and $uriA[3] > 0 and 'formulario' == $uriA[4]) {
                $ctx->contextoSecundario = 'RECURSO-VISUALIZAR';
                /** @var FrigaEditalEtapa $etapa */
                $etapa = $aux->etapa[$uriA[3]];
                if ('POST' == $this->metodo) {
                    $ctx->msg = "Submissão de recurso para  inscrição:{$uriA[5]}  na etapa:" . $etapa->getDescricao();
                } else {
                    $ctx->msg = 'CANDIDATO: Visualização do formulário de submissão de recurso na etapa:' . $etapa->getDescricao();
                }
            }
        }

        return $ctx;
    }
}
