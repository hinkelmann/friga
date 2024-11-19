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

namespace Nte\UsuarioBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaClassificacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaConvocacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoProjetoParticipante;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Usuario.
 *
 * @ORM\Table(name="fos_usuario")
 *
 * @ORM\Entity
 *
 * @ORM\HasLifecycleCallbacks()
 *
 * @UniqueEntity(fields={"email"}, message="Este valor já existe, por favor escolha outro")
 * @UniqueEntity(fields={"username"}, message="Este valor já existe, por favor escolha outro")
 */
class Usuario extends BaseUser
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(type="integer")
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="img", type="string", length=255, nullable=true)
     */
    protected $img = 'assets/img/default_user.png';

    /**
     * @var string
     *
     * @ORM\Column(name="nome", type="string", length=200, nullable=true)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="nome_social", type="string", length=200, nullable=true)
     */
    private $nomeSocial;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="data_nascimento", type="date", nullable=true)
     */
    private $dataNascimento;

    /**
     * @var string
     *
     * @ORM\Column(name="cpf", type="string", length=15, nullable=true)
     */
    private $cpf;

    /**
     * @var string
     *
     * @ORM\Column(name="rg_nro", type="string", length=25, nullable=true)
     */
    private $rgNro;

    /**
     * @var string
     *
     * @ORM\Column(name="te_nro", type="string", length=225, nullable=true)
     */
    private $teNro;

    /**
     * @var string
     *
     * @ORM\Column(name="cr_nro", type="string", length=225, nullable=true)
     */
    private $crNro;

    /**
     * @var string
     *
     * @ORM\Column(name="rg_orgao_expedidor", type="string", length=25, nullable=true)
     */
    private $rgOrgaoExpedidor;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="rg_data_expedicao", type="date", nullable=true)
     */
    private $rgDataExpedicao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="te_data_expedicao", type="date", nullable=true)
     */
    private $teDataExpedicao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cr_data_expedicao", type="date", nullable=true)
     */
    private $crDataExpedicao;

    /**
     * @var string
     *
     * @ORM\Column(name="contato_telefone1", type="string", length=45, nullable=true)
     */
    private $contatoTelefone1;

    /**
     * @var string
     *
     * @ORM\Column(name="contato_telefone2", type="string", length=45, nullable=true)
     */
    private $contatoTelefone2;

    /**
     * @var string
     *
     * @ORM\Column(name="contato_email", type="string", length=100, nullable=true)
     */
    private $contatoEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="endereco_cep", type="string", length=45, nullable=true)
     */
    private $enderecoCep;

    /**
     * @var string
     *
     * @ORM\Column(name="endereco_bairro", type="string", length=200, nullable=true)
     */
    private $enderecoBairro;

    /**
     * @var string
     *
     * @ORM\Column(name="endereco_logradouro", type="string", length=200, nullable=true)
     */
    private $enderecoLogradouro;

    /**
     * @var string
     *
     * @ORM\Column(name="endereco_numero", type="string", length=45, nullable=true)
     */
    private $enderecoNumero;

    /**
     * @var string
     *
     * @ORM\Column(name="endereco_complemento", type="string", length=200, nullable=true)
     */
    private $enderecoComplemento;

    /**
     * @var string
     *
     * @ORM\Column(name="endereco_municipio", type="string", length=200, nullable=true)
     */
    private $enderecoMunicipio;

    /**
     * @var string
     *
     * @ORM\Column(name="endereco_uf", type="string", length=2, nullable=true)
     */
    private $enderecoUf;

    /**
     * @var string
     *
     * @ORM\Column(name="lattes", type="string", length=255, nullable=true)
     */
    private $lattes;

    /**
     * @var string
     *
     * @ORM\Column(name="profissao", type="string", length=255, nullable=true)
     */
    private $profissao;

    /**
     * @var string
     *
     * @ORM\Column(name="escolaridade", type="string", length=255, nullable=true)
     */
    private $escolaridade;

    /**
     * @var string
     *
     * @ORM\Column(name="extra", type="text",  nullable=true)
     */
    private $extra;

    /**
     * @var int
     *
     * @ORM\Column(name="request_update", type="integer", nullable=true)
     */
    private $update;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetime", nullable=true)
     */
    private $updateDate;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario", mappedBy="idUsuario")
     */
    private $idEditalUsuario;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuarioConvite", mappedBy="idUsuario")
     */
    private $idEditalUsuarioConvite;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo", mappedBy="idUsuario")
     */
    private $idArquivo;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao", mappedBy="idUsuario")
     */
    private $inscricao;
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoProjetoParticipante", mappedBy="idUsuario")
     */
    private $idProjetoParticipante;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaConvocacao", mappedBy="idUsuario")
     */
    private $convocacao;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoPontuacaoAvaliacao", mappedBy="idAvaliador")
     */
    private $avaliacao;
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapaUsuario", mappedBy="idUsuario")
     */
    private $idEditalEtapaUsuario;

    /**
     * Usuario constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->roles = ['ROLE_USER'];
        $this->inscricao = new ArrayCollection();
        $this->idProjetoParticipante = new ArrayCollection();
        $this->idArquivo = new ArrayCollection();
        $this->idEditalUsuario = new ArrayCollection();
        $this->idEditalUsuarioConvite = new ArrayCollection();
        $this->convocacao = new ArrayCollection();
        $this->avaliacao = new ArrayCollection();
        $this->idEditalUsuario = new ArrayCollection();
        $this->update = 1;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return Usuario
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param string $nome
     *
     * @return Usuario
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * @return string
     */
    public function getNomeSocial()
    {
        return $this->nomeSocial;
    }

    /**
     * @param string $nomeSocial
     *
     * @return Usuario
     */
    public function setNomeSocial($nomeSocial)
    {
        $this->nomeSocial = $nomeSocial;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDataNascimento()
    {
        return $this->dataNascimento;
    }

    /**
     * @param \DateTime $dataNascimento
     *
     * @return Usuario
     */
    public function setDataNascimento($dataNascimento)
    {
        $this->dataNascimento = $dataNascimento;

        return $this;
    }

    /**
     * @return string
     */
    public function getCpf()
    {
        return $this->cpf;
    }

    /**
     * @param string $cpf
     *
     * @return Usuario
     */
    public function setCpf($cpf)
    {
        $this->cpf = $cpf;

        return $this;
    }

    /**
     * @return string
     */
    public function getRgNro()
    {
        return $this->rgNro;
    }

    /**
     * @param string $rgNro
     *
     * @return Usuario
     */
    public function setRgNro($rgNro)
    {
        $this->rgNro = $rgNro;

        return $this;
    }

    /**
     * @return string
     */
    public function getTeNro()
    {
        return $this->teNro;
    }

    /**
     * @param string $teNro
     *
     * @return Usuario
     */
    public function setTeNro($teNro)
    {
        $this->teNro = $teNro;

        return $this;
    }

    /**
     * @return string
     */
    public function getCrNro()
    {
        return $this->crNro;
    }

    /**
     * @param string $crNro
     *
     * @return Usuario
     */
    public function setCrNro($crNro)
    {
        $this->crNro = $crNro;

        return $this;
    }

    /**
     * @return string
     */
    public function getRgOrgaoExpedidor()
    {
        return $this->rgOrgaoExpedidor;
    }

    /**
     * @param string $rgOrgaoExpedidor
     *
     * @return Usuario
     */
    public function setRgOrgaoExpedidor($rgOrgaoExpedidor)
    {
        $this->rgOrgaoExpedidor = $rgOrgaoExpedidor;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRgDataExpedicao()
    {
        return $this->rgDataExpedicao;
    }

    /**
     * @param \DateTime $rgDataExpedicao
     *
     * @return Usuario
     */
    public function setRgDataExpedicao($rgDataExpedicao)
    {
        $this->rgDataExpedicao = $rgDataExpedicao;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTeDataExpedicao()
    {
        return $this->teDataExpedicao;
    }

    /**
     * @param \DateTime $teDataExpedicao
     *
     * @return Usuario
     */
    public function setTeDataExpedicao($teDataExpedicao)
    {
        $this->teDataExpedicao = $teDataExpedicao;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCrDataExpedicao()
    {
        return $this->crDataExpedicao;
    }

    /**
     * @param \DateTime $crDataExpedicao
     *
     * @return Usuario
     */
    public function setCrDataExpedicao($crDataExpedicao)
    {
        $this->crDataExpedicao = $crDataExpedicao;

        return $this;
    }

    /**
     * @return string
     */
    public function getContatoTelefone1()
    {
        return $this->contatoTelefone1;
    }

    /**
     * @param string $contatoTelefone1
     *
     * @return Usuario
     */
    public function setContatoTelefone1($contatoTelefone1)
    {
        $this->contatoTelefone1 = $contatoTelefone1;

        return $this;
    }

    /**
     * @return string
     */
    public function getContatoTelefone2()
    {
        return $this->contatoTelefone2;
    }

    /**
     * @param string $contatoTelefone2
     *
     * @return Usuario
     */
    public function setContatoTelefone2($contatoTelefone2)
    {
        $this->contatoTelefone2 = $contatoTelefone2;

        return $this;
    }

    /**
     * @return string
     */
    public function getContatoEmail()
    {
        return $this->contatoEmail;
    }

    /**
     * @param string $contatoEmail
     *
     * @return Usuario
     */
    public function setContatoEmail($contatoEmail)
    {
        $this->contatoEmail = $contatoEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getEnderecoCep()
    {
        return $this->enderecoCep;
    }

    /**
     * @param string $enderecoCep
     *
     * @return Usuario
     */
    public function setEnderecoCep($enderecoCep)
    {
        $this->enderecoCep = $enderecoCep;

        return $this;
    }

    /**
     * @return string
     */
    public function getEnderecoBairro()
    {
        return $this->enderecoBairro;
    }

    /**
     * @param string $enderecoBairro
     *
     * @return Usuario
     */
    public function setEnderecoBairro($enderecoBairro)
    {
        $this->enderecoBairro = $enderecoBairro;

        return $this;
    }

    /**
     * @return string
     */
    public function getEnderecoLogradouro()
    {
        return $this->enderecoLogradouro;
    }

    /**
     * @param string $enderecoLogradouro
     *
     * @return Usuario
     */
    public function setEnderecoLogradouro($enderecoLogradouro)
    {
        $this->enderecoLogradouro = $enderecoLogradouro;

        return $this;
    }

    /**
     * @return string
     */
    public function getEnderecoNumero()
    {
        return $this->enderecoNumero;
    }

    /**
     * @param string $enderecoNumero
     *
     * @return Usuario
     */
    public function setEnderecoNumero($enderecoNumero)
    {
        $this->enderecoNumero = $enderecoNumero;

        return $this;
    }

    /**
     * @return string
     */
    public function getEnderecoComplemento()
    {
        return $this->enderecoComplemento;
    }

    /**
     * @param string $enderecoComplemento
     *
     * @return Usuario
     */
    public function setEnderecoComplemento($enderecoComplemento)
    {
        $this->enderecoComplemento = $enderecoComplemento;

        return $this;
    }

    /**
     * @return string
     */
    public function getEnderecoMunicipio()
    {
        return $this->enderecoMunicipio;
    }

    /**
     * @param string $enderecoMunicipio
     *
     * @return Usuario
     */
    public function setEnderecoMunicipio($enderecoMunicipio)
    {
        $this->enderecoMunicipio = $enderecoMunicipio;

        return $this;
    }

    /**
     * @return string
     */
    public function getEnderecoUf()
    {
        return $this->enderecoUf;
    }

    /**
     * @param string $enderecoUf
     *
     * @return Usuario
     */
    public function setEnderecoUf($enderecoUf)
    {
        $this->enderecoUf = $enderecoUf;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getIdEditalUsuario()
    {
        return $this->idEditalUsuario;
    }

    /**
     * @return ArrayCollection
     */
    public function getIdArquivo()
    {
        return $this->idArquivo;
    }

    /**
     * @return ArrayCollection
     */
    public function getIdArquivoInscricao($inscricao)
    {
        $tmp = new ArrayCollection();

        return $this->idArquivo->filter(function(FrigaArquivo $item) use ($inscricao) {
            return \is_object($item->getInscricaoContexto())
                and $item->getInscricaoContexto()->getId() == $inscricao;
        });
    }

    /**
     * @return ArrayCollection
     */
    public function getInscricao()
    {
        return $this->inscricao;
    }

    /**
     * @return string
     */
    public function getLattes()
    {
        return $this->lattes;
    }

    /**
     * @param string $lattes
     *
     * @return Usuario
     */
    public function setLattes($lattes)
    {
        $this->lattes = $lattes;

        return $this;
    }

    /**
     * @return string
     */
    public function getProfissao()
    {
        return $this->profissao;
    }

    /**
     * @param string $profissao
     *
     * @return Usuario
     */
    public function setProfissao($profissao)
    {
        $this->profissao = $profissao;

        return $this;
    }

    /**
     * @return string
     */
    public function getEscolaridade()
    {
        return $this->escolaridade;
    }

    /**
     * @param string $escolaridade
     *
     * @return Usuario
     */
    public function setEscolaridade($escolaridade)
    {
        $this->escolaridade = $escolaridade;

        return $this;
    }

    /**
     * @return string
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param string $extra
     *
     * @return Usuario
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * @return int
     */
    public function getUpdate()
    {
        return $this->update;
    }

    /**
     * @param int $update
     *
     * @return Usuario
     */
    public function setUpdate($update)
    {
        $this->update = $update;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * @param \DateTime $updateDate
     *
     * @return Usuario
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * @return array
     */
    public static function getRolesNames()
    {
        return [
            'ADMINISTRADOR' => 'ROLE_ADMIN',
            'ADMINISTRADOR DE CONTAS DE USUÁRIO' => 'ROLE_ADMIN_USER',
            'EDITAL - ADMINISTRADOR DE EDITAL' => 'ROLE_ADMIN_EDITAL',
            'EDITAL - DOWNLOAD DE ARQUIVO' => 'ROLE_ADMIN_ARQUIVO',
            'SUPORTE TÉCNICO' => 'ROLE_SUPORTE',
            'EDITAL - RELATÓRIOS GERENCIAIS' => 'ROLE_GERENCIAL',
            'EDITAL - AVALIADOR' => 'ROLE_AVALIADOR',
            'AUDITOR' => 'ROLE_AUDITOR',
            'COMUM' => 'ROLE_USER',
        ];
    }

    public function getRolesExtenco()
    {
        $str = [];
        foreach ($this->roles as $role) {
            $arrr = \array_flip(self::getRolesNames());
            if (\key_exists($role, $arrr)) {
                $str[] = $arrr[$role];
            }
        }

        return \implode(', ', $str);
    }

    /**
     * @return array
     */
    public static function getRoles2()
    {
        return [
            'ROLE_ADMIN' => 'ADMINISTRADOR',
            'ROLE_ADMIN_USER' => 'ADMINISTRADOR DE CONTAS DE USUÁRIO',
            'ROLE_ADMIN_EDITAL' => 'EDITAL - ADMINISTRADOR DE EDITAL',
            'ROLE_ADMIN_ARQUIVO' => 'EDITAL - DOWNLOAD DE ARQUIVO',
            'ROLE_GERENCIAL' => 'EDITAL - RELATÓRIOS GERENCIAIS',
            'ROLE_AVALIADOR' => 'EDITAL - AVALIADOR',
            'ROLE_AUDITOR' => 'AUDITOR',
            'ROLE_USER' => 'COMUM',
            'ROLE_SUPORTE' => 'SUPORTE TÉCNICO',
        ];
    }

    public function getHighestRole()
    {
        $rolesSortedByImportance = [
            'ROLE_ADMIN',
            'ROLE_ADMIN_USER',
            'ROLE_SUPORTE',
            'ROLE_GERENCIAL',
            'ROLE_AVALIADOR',
            'ROLE_AUDITOR',
            'ROLE_USER'];
        foreach ($rolesSortedByImportance as $role) {
            if (\in_array($role, $this->roles)) {
                return $role;
            }
        }

        return false;
    }

    public function getHighestRoleName()
    {
        return self::getRoles2()[$this->getHighestRole()];
    }

    /**
     * Set img.
     *
     * @param string $img
     *
     * @return Usuario
     */
    public function setImg($img)
    {
        $this->img = $img;

        return $this;
    }

    /**
     * Get img.
     *
     * @return string
     */
    public function getImg()
    {
        /** @var FrigaArquivo $arquivo */
        foreach ($this->idArquivo as $arquivo) {
            if ('PERFIL' == $arquivo->getContexto() and 100 == $arquivo->getAtributo()) {
                return $arquivo->getId();
            }
        }

        return null;
    }

    /**
     * @param string $role
     *
     * @return $this
     */
    public function addRole($role)
    {
        $role = \strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }
        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function setRoles(array $roles)
    {
        $this->roles = [];
        foreach ($roles as $role => $id) {
            $this->addRole($id->id);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getIdEditalUsuarioConvite()
    {
        return $this->idEditalUsuarioConvite;
    }

    /**
     * @return ArrayCollection
     */
    public function getConvocacao()
    {
        return $this->convocacao;
    }

    /**
     * @return ArrayCollection
     */
    public function getConvocacoes()
    {
        $tmp = new ArrayCollection();
        /** @var FrigaInscricao $inscricao */
        foreach ($this->getInscricao() as $inscricao) {
            $tmp = new ArrayCollection(\array_merge($tmp->toArray(), $inscricao->getConvocacao()->toArray()));
        }

        return $tmp->filter(function(FrigaConvocacao $c) {
            return $c->getIdEtapa()->getPeriodoDivulgacao();
        });
    }

    /**
     * @return ArrayCollection
     */
    public function getClassificacoes()
    {
        $tmp = new ArrayCollection();
        /** @var FrigaInscricao $inscricao */
        foreach ($this->getInscricao() as $inscricao) {
            $tmp = new ArrayCollection(\array_merge($tmp->toArray(), $inscricao->getClassificacao()->toArray()));
        }

        return $tmp->filter(function(FrigaClassificacao $c) {
            return $c->getIdEtapa()->getPeriodoDivulgacao();
        });
    }

    /**
     * @return ArrayCollection
     */
    public function getRecursoEtapa()
    {
        $tmp = new ArrayCollection();
        /** @var FrigaInscricao $inscricao */
        foreach ($this->getInscricao() as $inscricao) {
            if (-999 != $inscricao->getIdSituacao()) {
                $tmp = new ArrayCollection(\array_merge($tmp->toArray(), $inscricao->getRecursoEtapa()->toArray()));
            }
        }

        return $tmp;
    }

    /**
     * @return ArrayCollection
     */
    public function getRecursoInscricao()
    {
        $tmp = new ArrayCollection();
        /** @var FrigaInscricao $inscricao */
        foreach ($this->getInscricao() as $inscricao) {
            if (-999 != $inscricao->getIdSituacao()) {
                $tmp = new ArrayCollection(\array_merge($tmp->toArray(), $inscricao->getRecursos()->toArray()));
            }
        }

        return $tmp;
    }

    public function getInscicaoValida()
    {
        return $this->inscricao->filter(function(FrigaInscricao $i) {
            return -999 != $i->getIdSituacao();
        });
    }

    public function getInscricaoProjetoValida()
    {
        return $this->idProjetoParticipante->filter(function(FrigaInscricaoProjetoParticipante $i) {
            return 1 == $i->getConfirmacao() and -999 != $i->getIdInscricao()->getIdSituacao();
        });
    }

    public function getTotalInscricaoValida()
    {
        return $this->getInscicaoValida()->count() + $this->getInscricaoProjetoValida()->count();
    }

    /**
     * @return ArrayCollection
     */
    public function getAvaliacao()
    {
        return $this->avaliacao;
    }

    /**
     * @return ArrayCollection
     */
    public function getIdProjetoParticipante()
    {
        return $this->idProjetoParticipante;
    }

    /**
     * @return ArrayCollection
     */
    public function getIdEditalEtapaUsuario()
    {
        return $this->idEditalEtapaUsuario;
    }

    /**
     * @return \stdClass
     */
    public function getProgressoEtapa(FrigaEditalEtapa $etapa)
    {
        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->eq('idEtapa', $etapa));
        $pe = $this->idEditalEtapaUsuario->matching($criteria);
        $obj = new \stdClass();
        $obj->feito = $pe->count();
        $obj->total = $etapa->getAvaliacao()->count();

        return $obj;
    }

    /**
     * @return bool
     */
    public function getTermoCompromisso(FrigaEditalEtapa $etapa)
    {
        /** @var FrigaEditalUsuario $item */
        foreach ($this->getEtapaEditalUsuario($etapa->getIdEdital()) as $item) {
            return $item->isTermoCompromisso();
        }

        return false;
    }

    /**
     * @return ArrayCollection;
     */
    public function getEtapaEditalUsuario(FrigaEdital $edital)
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('idEdital.id', $edital->getId()));

        foreach ($this->idEditalUsuario as $item) {
        }

        //   dump($this->idEditalUsuario->matching($criteria)->count());
        foreach ($this->idEditalUsuario->matching($criteria) as $item) {
            //       dump($item);
        }

        return $this->idEditalUsuario->matching($criteria);
    }

    /**
     * @return bool
     */
    public function getPermissaoEtapa(FrigaEditalEtapa $etapa)
    {
        /** @var FrigaEditalUsuario $item */
        foreach ($this->getEtapaEditalUsuario($etapa->getIdEdital()) as $item) {
            switch ($etapa->getTipo()) {
                case 3:
                case 7:
                    return $item->isAvaliador() && $item->isTermoCompromisso();
                case 4:
                    return $item->isResultado() && $item->isTermoCompromisso();
                case 5:
                    return $item->isConvocacao() && $item->isTermoCompromisso();
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function getPermissaoEtapaTermo(FrigaEditalEtapa $etapa)
    {
        /** @var FrigaEditalUsuario $item */
        foreach ($this->getEtapaEditalUsuario($etapa->getIdEdital()) as $item) {
            switch ($etapa->getTipo()) {
                case 3:
                case 7:
                    return $item->isAvaliador() && \is_null($item->isTermoCompromisso());
                case 4:
                    return $item->isResultado() && \is_null($item->isTermoCompromisso());
                case 5:
                    return $item->isConvocacao() && \is_null($item->isTermoCompromisso());
            }
        }

        return false;
    }

    /**
     * @return \stdClass
     */
    public function getPermissoesEdital(FrigaEdital $edital)
    {
        $obj = new \stdClass();
        $obj->administrador = false;
        $obj->avaliador = false;
        $obj->resultado = false;
        $obj->convocacao = false;

        /** @var FrigaEditalUsuario $item */
        foreach ($this->getEtapaEditalUsuario($edital) as $item) {
            if ($item->isAdministrador()) {
                $obj->administrador = true;
            }
            if ($item->isAvaliador()) {
                $obj->avaliador = true;
            }
            if ($item->isResultado()) {
                $obj->resultado = true;
            }
            if ($item->isConvocacao()) {
                $obj->convocacao = true;
            }
        }

        return $obj;
    }

    /**
     * @return bool
     */
    public function getPermissaoEdital(FrigaEdital $edital)
    {
        /** @var FrigaEditalUsuario $item */
        foreach ($this->getEtapaEditalUsuario($edital) as $item) {
            if ($item->isAdministrador()) {
                return true;
            }
        }

        return false;
    }
}
