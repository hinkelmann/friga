<?php

namespace Nte\UsuarioBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr\Func;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaConvocacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCargo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaClassificacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoProjetoParticipante;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Usuario
 *
 * @ORM\Table(name="fos_usuario")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"email"}, message="Este valor já existe, por favor escolha outro")
 * @UniqueEntity(fields={"username"}, message="Este valor já existe, por favor escolha outro")
 */
class Usuario extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario", mappedBy="idUsuario")
     */
    private $idEditalUsuario;

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
     * Usuario constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->roles = ['ROLE_USER'];
        $this->inscricao = new ArrayCollection();
        $this->idProjetoParticipante = new ArrayCollection();
        $this->idArquivo = new ArrayCollection();
        $this->convocacao = new ArrayCollection();
        $this->avaliacao = new ArrayCollection();

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
     * @return Usuario
     */
    public function setLattes($lattes)
    {
        $this->lattes = $lattes;
        return $this;
    }




    /**
     * @return array
     */
    public static function getRolesNames()
    {
        return [
            "Usuário Administrador" => "ROLE_ADMIN",
            "Usuário Gerente de Usuários" => "ROLE_ADMIN_USER",
            "Usuário Gerente de Edital" => "ROLE_ADMIN_EDITAL",
            "Usuário Gerente de Arquivo" => "ROLE_ADMIN_ARQUIVO",
            "Usuário Suporte" => "ROLE_SUPORTE",
            "Usuário Gerencial" => "ROLE_GERENCIAL",
            "Usuário Avaliador" => "ROLE_AVALIADOR",
            "Usuário Comum" => "ROLE_USER",
        ];
    }

    /**
     * @return array
     */
    public static function getRoles2()
    {
        return [
            "ROLE_ADMIN" => "Usuário Administrador",
            "ROLE_ADMIN_USER" => "Usuário Gerente de Usuários",
            "ROLE_ADMIN_EDITAL" => "Usuário Gerente de Edital",
            "ROLE_ADMIN_ARQUIVO" => "Usuário Gerente de Arquivo",
            "ROLE_GERENCIAL" => "Usuário Gerencial",
            "ROLE_AVALIADOR" => "Usuário Avaliador",
            "ROLE_USER" => "Usuário Comum",
            "ROLE_SUPORTE" => "Usuário Suporte",
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
            'ROLE_USER'];
        foreach ($rolesSortedByImportance as $role) {
            if (in_array($role, $this->roles)) {
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
     * Set img
     * @param string $img
     * @return Usuario
     */
    public function setImg($img)
    {
        $this->img = $img;
        return $this;
    }

    /**
     * Get img
     * @return string
     */
    public function getImg()
    {
        /** @var FrigaArquivo $arquivo */
        foreach ($this->idArquivo as $arquivo) {
            if ($arquivo->getContexto() == "PERFIL" and $arquivo->getAtributo() == 100) {
                return $arquivo->getId();
            }
        }
        return null;
    }

    /**
     * @param string $role
     * @return $this
     */
    public function addRole($role)
    {

        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
        return $this;
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles(array $roles)
    {
        $this->roles = array();
        foreach ($roles as $role => $id) {
            $this->addRole($id->id);
        }
        return $this;
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
            $tmp = new ArrayCollection(array_merge($tmp->toArray(), $inscricao->getConvocacao()->toArray()));
        }
        return $tmp->filter(function (FrigaConvocacao $c) {
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
            $tmp = new ArrayCollection(array_merge($tmp->toArray(), $inscricao->getClassificacao()->toArray()));
        }
        return $tmp->filter(function (FrigaClassificacao $c) {
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
            if ($inscricao->getIdSituacao() != -999) {
                $tmp = new ArrayCollection(array_merge($tmp->toArray(), $inscricao->getRecursoEtapa()->toArray()));
            }
        }
       return  $tmp;
    }
    /**
     * @return ArrayCollection
     */
    public function getRecursoInscricao()
    {
        $tmp = new ArrayCollection();
        /** @var FrigaInscricao $inscricao */
        foreach ($this->getInscricao() as $inscricao) {
            if ($inscricao->getIdSituacao() != -999) {
                $tmp = new ArrayCollection(array_merge($tmp->toArray(), $inscricao->getRecursos()->toArray()));
            }
        }
        return  $tmp;
    }

    public function getInscicaoValida(){
        return $this->inscricao->filter(function (FrigaInscricao $i){
            return $i->getIdSituacao() != -999;
        });
    }

    public function getInscricaoProjetoValida(){
        return $this->idProjetoParticipante->filter(function (FrigaInscricaoProjetoParticipante $i){
            return $i->getConfirmacao()==1 and  $i->getIdInscricao()->getIdSituacao() != -999;
        });
    }

    public function getTotalInscricaoValida(){
      return  $this->getInscicaoValida()->count() + $this->getInscricaoProjetoValida()->count();
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


}
