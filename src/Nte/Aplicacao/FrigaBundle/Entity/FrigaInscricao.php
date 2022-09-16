<?php

namespace Nte\Aplicacao\FrigaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Monolog\Handler\IFTTTHandler;
use Nte\UsuarioBundle\Entity\Usuario;
use Symfony\Component\HttpFoundation\Request;

/**
 * FrigaInscricao
 *
 * @ORM\Table(name="friga_inscricao")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class FrigaInscricao
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
     * @var integer
     *
     * @ORM\Column(name="id_situacao", type="integer", nullable=true)
     */
    private $idSituacao;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_situacao_anterior", type="integer", nullable=true)
     */
    private $idSituacaoAnterior;

    /**
     * @var integer
     *
     * @ORM\Column(name="posicao", type="integer", nullable=true)
     */
    private $posicao;


    /**
     * @var string
     *
     * @ORM\Column(name="nome", type="string", length=200, nullable=true)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="projeto_titulo", type="text", nullable=true)
     */
    private $projetoTitulo;

    /**
     * @var string
     *
     * @ORM\Column(name="projeto_resumo", type="text", nullable=true)
     */
    private $projetoResumo;

    /**
     * @var string
     *
     * @ORM\Column(name="projeto_area_conhecimento", type="string", length=255, nullable=true)
     */
    private $projetoAreaConhecimento;

    /**
     * @var integer
     *
     * @ORM\Column(name="sexo", type="integer", nullable=true)
     */
    private $sexo;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="data_nascimento", type="date", nullable=true)
     */
    private $dataNascimento;

    /**
     * @var boolean
     *
     * @ORM\Column(name="estrangeiro", type="boolean", nullable=true)
     */
    private $estrangeiro;

    /**
     * @var string
     *
     * @ORM\Column(name="nacionalidade", type="string", length=255, nullable=true)
     */
    private $nacionalidade;

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
     * @ORM\Column(name="matricula_nro", type="string", length=225, nullable=true)
     */
    private $matriculaNro;

    /**
     * @var string
     *
     * @ORM\Column(name="matricula_curso", type="string", length=225, nullable=true)
     */
    private $matriculaCurso;

    /**
     * @var integer
     *
     * @ORM\Column(name="matricula_beneficio", type="integer",  nullable=true)
     */
    private $matriculaBeneficio;

    /**
     * @var string
     * @ORM\Column(name="matricula_indice_desempenho", type="string", length=225, nullable=true)
     */
    private $matriculaIndiceDesempenho;


    /**
     * @var string
     *
     * @ORM\Column(name="recebimento_bolsa", type="text",  nullable=true)
     */
    private $recebimentoBolsa;

    /**
     * @var string
     *
     * @ORM\Column(name="certidao_nascimento_nro", type="string", length=225, nullable=true)
     */
    private $certidaoNascimentoNro;

    /**
     * @var string
     *
     * @ORM\Column(name="passaporte_nro", type="string", length=225, nullable=true)
     */
    private $passaporteNro;

    /**
     * @var string
     *
     * @ORM\Column(name="rne_nro", type="string", length=225, nullable=true)
     */
    private $rneNro;

    /**
     * @var string
     *
     * @ORM\Column(name="rg_uf", type="string", length=25, nullable=true)
     */
    private $rgUF;

    /**
     * @var string
     *
     * @ORM\Column(name="url0", type="text", length=2000, nullable=true)
     */
    private $url0;

    /**
     * @var string
     *
     * @ORM\Column(name="url1",  type="text", length=2000,  nullable=true)
     */
    private $url1;

    /**
     * @var string
     *
     * @ORM\Column(name="url2", type="text", length=2000,  nullable=true)
     */
    private $url2;

    /**
     * @var string
     *
     * @ORM\Column(name="url3", type="text", length=2000,  nullable=true)
     */
    private $url3;


    /**
     * @var string
     *
     * @ORM\Column(name="url4",  type="text", length=2000, nullable=true)
     */
    private $url4;

    /**
     * @var string
     *
     * @ORM\Column(name="apresentacao",  type="text", nullable=true)
     */
    private $apresentacao;

    /**
     * @var string
     *
     * @ORM\Column(name="banco_instituicao", type="string", length=225, nullable=true)
     */
    private $bancoInstituicao;

    /**
     * @var string
     *
     * @ORM\Column(name="banco_agencia", type="string", length=225, nullable=true)
     */
    private $bancoAgencia;

    /**
     * @var string
     *
     * @ORM\Column(name="banco_conta", type="string", length=225, nullable=true)
     */
    private $bancoConta;


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
     * @var \DateTime
     *
     * @ORM\Column(name="rne_data_expedicao", type="date", nullable=true)
     */
    private $rneDataExpedicao;

    /**
     * @var string
     *
     * @ORM\Column(name="rne_data_validade", type="date", nullable=true)
     */
    private $rneDataValidade;

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
     * @ORM\Column(name="uuid", type="string", length=50, nullable=true)
     */
    private $uuid;

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

    /**
     * @var FrigaEditalCargo
     *
     * @ORM\ManyToOne(targetEntity="FrigaEditalCargo", inversedBy="idEditalUsuarioInscrito", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_cargo", referencedColumnName="id")
     * })
     */
    private $idCargo;

    /**
     * @var FrigaEditalCargo
     *
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCota", inversedBy="idEditalUsuarioInscrito", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_cota", referencedColumnName="id")
     * })
     */
    private $idCota;

    /**
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="Nte\UsuarioBundle\Entity\Usuario", inversedBy="inscricao", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
     * })
     */
    private $idUsuario;


    /**
     * @var FrigaEdital
     *
     * @ORM\ManyToOne(targetEntity="FrigaEdital", inversedBy="inscricao", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_edital", referencedColumnName="id")
     * })
     */
    private $idEdital;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoPontuacao", mappedBy="idInscricao", fetch="EXTRA_LAZY")
     */
    private $pontuacao;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaConvocacao", mappedBy="idInscricao", fetch="EXTRA_LAZY")
     */
    private $convocacao;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoPontuacaoAvaliacao", mappedBy="idInscricao", fetch="EXTRA_LAZY")
     */
    private $avaliacao;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaClassificacao", mappedBy="idInscricao")
     */
    private $classificacao;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoFeedback", mappedBy="idInscricao")
     */
    private $feedback;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoRecurso", mappedBy="idInscricao")
     */
    private $recursos;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoProjetoParticipante", mappedBy="idInscricao")
     */
    private $idProjetoParticipante;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="FrigaArquivo", inversedBy="idInscricao", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="friga_inscricao_tem_arquivo",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_inscricao", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_arquivo", referencedColumnName="id")
     *   }
     * )
     */
    private $idArquivo;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idArquivo = new ArrayCollection();
        $this->pontuacao = new ArrayCollection();
        $this->avaliacao = new ArrayCollection();
        $this->convocacao = new ArrayCollection();
        $this->recursos = new ArrayCollection();
        $this->classificacao = new ArrayCollection();
        $this->feedback = new ArrayCollection();
        $this->idProjetoParticipante = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idSituacao
     *
     * @param integer $idSituacao
     *
     * @return FrigaInscricao
     */
    public function setIdSituacao($idSituacao)
    {
        $this->idSituacao = $idSituacao;

        return $this;
    }

    /**
     * Get idSituacao
     *
     * @return integer
     */
    public function getIdSituacao()
    {
        return $this->idSituacao;
    }

    /**
     * @return int
     */
    public function getIdSituacaoAnterior()
    {
        return $this->idSituacaoAnterior;
    }

    /**
     * @param int $idSituacaoAnterior
     * @return FrigaInscricao
     */
    public function setIdSituacaoAnterior($idSituacaoAnterior)
    {
        $this->idSituacaoAnterior = $idSituacaoAnterior;
        return $this;
    }


    /**
     * Set posicao
     *
     * @param integer $posicao
     *
     * @return FrigaInscricao
     */
    public function setPosicao($posicao)
    {
        $this->posicao = $posicao;

        return $this;
    }

    /**
     * Get posicao
     *
     * @return integer
     */
    public function getPosicao()
    {
        return $this->posicao;
    }

    /**
     * Set nome
     *
     * @param string $nome
     *
     * @return FrigaInscricao
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get nome
     *
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set dataNascimento
     *
     * @param \DateTime $dataNascimento
     *
     * @return FrigaInscricao
     */
    public function setDataNascimento($dataNascimento)
    {
        $this->dataNascimento = $dataNascimento;

        return $this;
    }

    /**
     * Get dataNascimento
     *
     * @return \DateTime
     */
    public function getDataNascimento()
    {
        return $this->dataNascimento;
    }

    /**
     * Set cpf
     *
     * @param string $cpf
     *
     * @return FrigaInscricao
     */
    public function setCpf($cpf)
    {
        $this->cpf = $cpf;

        return $this;
    }

    /**
     * Get cpf
     *
     * @return string
     */
    public function getCpf()
    {
        return $this->cpf;
    }

    /**
     * Set rgNro
     *
     * @param string $rgNro
     *
     * @return FrigaInscricao
     */
    public function setRgNro($rgNro)
    {
        $this->rgNro = $rgNro;

        return $this;
    }

    /**
     * Get rgNro
     *
     * @return string
     */
    public function getRgNro()
    {
        return $this->rgNro;
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
     * @return FrigaInscricao
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
     * @return FrigaInscricao
     */
    public function setCrNro($crNro)
    {
        $this->crNro = $crNro;
        return $this;
    }


    /**
     * Set rgOrgaoExpedidor
     *
     * @param string $rgOrgaoExpedidor
     *
     * @return FrigaInscricao
     */
    public function setRgOrgaoExpedidor($rgOrgaoExpedidor)
    {
        $this->rgOrgaoExpedidor = $rgOrgaoExpedidor;

        return $this;
    }

    /**
     * Get rgOrgaoExpedidor
     *
     * @return string
     */
    public function getRgOrgaoExpedidor()
    {
        return $this->rgOrgaoExpedidor;
    }

    /**
     * Set rgDataExpedicao
     *
     * @param \DateTime $rgDataExpedicao
     *
     * @return FrigaInscricao
     */
    public function setRgDataExpedicao($rgDataExpedicao)
    {
        $this->rgDataExpedicao = $rgDataExpedicao;

        return $this;
    }

    /**
     * Get rgDataExpedicao
     *
     * @return \DateTime
     */
    public function getRgDataExpedicao()
    {
        return $this->rgDataExpedicao;
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
     * @return FrigaInscricao
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
     * @return FrigaInscricao
     */
    public function setCrDataExpedicao($crDataExpedicao)
    {
        $this->crDataExpedicao = $crDataExpedicao;
        return $this;
    }

    /**
     * Set contatoTelefone1
     *
     * @param string $contatoTelefone1
     *
     * @return FrigaInscricao
     */
    public function setContatoTelefone1($contatoTelefone1)
    {
        $this->contatoTelefone1 = $contatoTelefone1;

        return $this;
    }

    /**
     * Get contatoTelefone1
     *
     * @return string
     */
    public function getContatoTelefone1()
    {
        return $this->contatoTelefone1;
    }

    /**
     * Set contatoTelefone2
     *
     * @param string $contatoTelefone2
     *
     * @return FrigaInscricao
     */
    public function setContatoTelefone2($contatoTelefone2)
    {
        $this->contatoTelefone2 = $contatoTelefone2;

        return $this;
    }

    /**
     * Get contatoTelefone2
     *
     * @return string
     */
    public function getContatoTelefone2()
    {
        return $this->contatoTelefone2;
    }

    /**
     * Set contatoEmail
     *
     * @param string $contatoEmail
     *
     * @return FrigaInscricao
     */
    public function setContatoEmail($contatoEmail)
    {
        $this->contatoEmail = $contatoEmail;

        return $this;
    }

    /**
     * Get contatoEmail
     *
     * @return string
     */
    public function getContatoEmail()
    {
        return $this->contatoEmail;
    }

    /**
     * Set enderecoCep
     *
     * @param string $enderecoCep
     *
     * @return FrigaInscricao
     */
    public function setEnderecoCep($enderecoCep)
    {
        $this->enderecoCep = $enderecoCep;

        return $this;
    }

    /**
     * Get enderecoCep
     *
     * @return string
     */
    public function getEnderecoCep()
    {
        return $this->enderecoCep;
    }

    /**
     * Set enderecoBairro
     *
     * @param string $enderecoBairro
     *
     * @return FrigaInscricao
     */
    public function setEnderecoBairro($enderecoBairro)
    {
        $this->enderecoBairro = $enderecoBairro;

        return $this;
    }

    /**
     * Get enderecoBairro
     *
     * @return string
     */
    public function getEnderecoBairro()
    {
        return $this->enderecoBairro;
    }

    /**
     * Set enderecoLogradouro
     *
     * @param string $enderecoLogradouro
     *
     * @return FrigaInscricao
     */
    public function setEnderecoLogradouro($enderecoLogradouro)
    {
        $this->enderecoLogradouro = $enderecoLogradouro;

        return $this;
    }

    /**
     * Get enderecoLogradouro
     *
     * @return string
     */
    public function getEnderecoLogradouro()
    {
        return $this->enderecoLogradouro;
    }

    /**
     * Set enderecoNumero
     *
     * @param string $enderecoNumero
     *
     * @return FrigaInscricao
     */
    public function setEnderecoNumero($enderecoNumero)
    {
        $this->enderecoNumero = $enderecoNumero;

        return $this;
    }

    /**
     * Get enderecoNumero
     *
     * @return string
     */
    public function getEnderecoNumero()
    {
        return $this->enderecoNumero;
    }

    /**
     * Set enderecoComplemento
     *
     * @param string $enderecoComplemento
     *
     * @return FrigaInscricao
     */
    public function setEnderecoComplemento($enderecoComplemento)
    {
        $this->enderecoComplemento = $enderecoComplemento;

        return $this;
    }

    /**
     * Get enderecoComplemento
     *
     * @return string
     */
    public function getEnderecoComplemento()
    {
        return $this->enderecoComplemento;
    }

    /**
     * @return \DateTime
     */
    public function getRneDataExpedicao()
    {
        return $this->rneDataExpedicao;
    }

    /**
     * @param \DateTime $rneDataExpedicao
     * @return FrigaInscricao
     */
    public function setRneDataExpedicao($rneDataExpedicao)
    {
        $this->rneDataExpedicao = $rneDataExpedicao;
        return $this;
    }


    /**
     * Set enderecoMunicipio
     *
     * @param string $enderecoMunicipio
     *
     * @return FrigaInscricao
     */
    public function setEnderecoMunicipio($enderecoMunicipio)
    {
        $this->enderecoMunicipio = $enderecoMunicipio;

        return $this;
    }

    /**
     * Get enderecoMunicipio
     *
     * @return string
     */
    public function getEnderecoMunicipio()
    {
        return $this->enderecoMunicipio;
    }

    /**
     * Set enderecoUf
     *
     * @param string $enderecoUf
     *
     * @return FrigaInscricao
     */
    public function setEnderecoUf($enderecoUf)
    {
        $this->enderecoUf = $enderecoUf;

        return $this;
    }

    /**
     * Get enderecoUf
     *
     * @return string
     */
    public function getEnderecoUf()
    {
        return $this->enderecoUf;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return FrigaInscricao
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @return bool
     */
    public function isEstrangeiro()
    {
        return $this->estrangeiro;
    }

    /**
     * @param bool $estrangeiro
     * @return FrigaInscricao
     */
    public function setEstrangeiro($estrangeiro)
    {
        $this->estrangeiro = $estrangeiro;
        return $this;
    }

    /**
     * @return string
     */
    public function getNacionalidade()
    {
        return $this->nacionalidade;
    }

    /**
     * @param string $nacionalidade
     * @return FrigaInscricao
     */
    public function setNacionalidade($nacionalidade)
    {
        $this->nacionalidade = $nacionalidade;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassaporteNro()
    {
        return $this->passaporteNro;
    }

    /**
     * @param string $passaporteNro
     * @return FrigaInscricao
     */
    public function setPassaporteNro($passaporteNro)
    {
        $this->passaporteNro = $passaporteNro;
        return $this;
    }

    /**
     * @return string
     */
    public function getRneNro()
    {
        return $this->rneNro;
    }

    /**
     * @param string $rneNro
     * @return FrigaInscricao
     */
    public function setRneNro($rneNro)
    {
        $this->rneNro = $rneNro;
        return $this;
    }

    /**
     * @return string
     */
    public function getRgUF()
    {
        return $this->rgUF;
    }

    /**
     * @param string $rgUF
     * @return FrigaInscricao
     */
    public function setRgUF($rgUF)
    {
        $this->rgUF = $rgUF;
        return $this;
    }


    /**
     * @return string
     */
    public function getCertidaoNascimentoNro()
    {
        return $this->certidaoNascimentoNro;
    }

    /**
     * @param string $certidaoNascimentoNro
     * @return FrigaInscricao
     */
    public function setCertidaoNascimentoNro($certidaoNascimentoNro)
    {
        $this->certidaoNascimentoNro = $certidaoNascimentoNro;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl0()
    {
        return $this->url0;
    }

    /**
     * @param string $url0
     * @return FrigaInscricao
     */
    public function setUrl0($url0)
    {
        $this->url0 = $url0;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl1()
    {
        return $this->url1;
    }

    /**
     * @param string $url1
     * @return FrigaInscricao
     */
    public function setUrl1($url1)
    {
        $this->url1 = $url1;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl2()
    {
        return $this->url2;
    }

    /**
     * @param string $url2
     * @return FrigaInscricao
     */
    public function setUrl2($url2)
    {
        $this->url2 = $url2;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl3()
    {
        return $this->url3;
    }

    /**
     * @param string $url3
     * @return FrigaInscricao
     */
    public function setUrl3($url3)
    {
        $this->url3 = $url3;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl4()
    {
        return $this->url4;
    }

    /**
     * @param string $url4
     * @return FrigaInscricao
     */
    public function setUrl4($url4)
    {
        $this->url4 = $url4;
        return $this;
    }

    /**
     * @return string
     */
    public function getBancoInstituicao()
    {
        return $this->bancoInstituicao;
    }

    /**
     * @param string $bancoInstituicao
     * @return FrigaInscricao
     */
    public function setBancoInstituicao($bancoInstituicao)
    {
        $this->bancoInstituicao = $bancoInstituicao;
        return $this;
    }

    /**
     * @return string
     */
    public function getBancoAgencia()
    {
        return $this->bancoAgencia;
    }

    /**
     * @param string $bancoAgencia
     * @return FrigaInscricao
     */
    public function setBancoAgencia($bancoAgencia)
    {
        $this->bancoAgencia = $bancoAgencia;
        return $this;
    }

    /**
     * @return string
     */
    public function getBancoConta()
    {
        return $this->bancoConta;
    }

    /**
     * @param string $bancoConta
     * @return FrigaInscricao
     */
    public function setBancoConta($bancoConta)
    {
        $this->bancoConta = $bancoConta;
        return $this;
    }


    /**
     * @return string
     */
    public function getApresentacao()
    {
        return $this->apresentacao;
    }

    /**
     * @param string $apresentacao
     * @return FrigaInscricao
     */
    public function setApresentacao($apresentacao)
    {
        $this->apresentacao = $apresentacao;
        return $this;
    }


    /**
     * Set registroDataCriacao
     *
     * @param \DateTime $registroDataCriacao
     *
     * @return FrigaInscricao
     */
    public function setRegistroDataCriacao($registroDataCriacao)
    {
        $this->registroDataCriacao = $registroDataCriacao;

        return $this;
    }

    /**
     * Get registroDataCriacao
     *
     * @return \DateTime
     */
    public function getRegistroDataCriacao()
    {
        return $this->registroDataCriacao;
    }

    /**
     * Set registroDataAtualizacao
     *
     * @param \DateTime $registroDataAtualizacao
     *
     * @return FrigaInscricao
     */
    public function setRegistroDataAtualizacao($registroDataAtualizacao)
    {
        $this->registroDataAtualizacao = $registroDataAtualizacao;

        return $this;
    }

    /**
     * Get registroDataAtualizacao
     *
     * @return \DateTime
     */
    public function getRegistroDataAtualizacao()
    {
        return $this->registroDataAtualizacao;
    }

    /**
     * Set idCargo
     *
     * @param FrigaEditalCargo $idCargo
     *
     * @return FrigaInscricao
     */
    public function setIdCargo(FrigaEditalCargo $idCargo = null)
    {
        $this->idCargo = $idCargo;

        return $this;
    }

    /**
     * Get idCargo
     *
     * @return FrigaEditalCargo
     */
    public function getIdCargo()
    {
        return $this->idCargo;
    }

    /**
     * Set idUsuario
     *
     * @param Usuario $idUsuario
     *
     * @return FrigaInscricao
     */
    public function setIdUsuario(Usuario $idUsuario = null)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * Get idUsuario
     *
     * @return Usuario
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /**
     * Set idEdital
     *
     * @param FrigaEdital $idEdital
     *
     * @return FrigaInscricao
     */
    public function setIdEdital(FrigaEdital $idEdital = null)
    {
        $this->idEdital = $idEdital;

        return $this;
    }

    /**
     * Get idEdital
     *
     * @return FrigaEdital
     */
    public function getIdEdital()
    {
        return $this->idEdital;
    }

    /**
     * @return FrigaEditalCargo
     */
    public function getIdCota()
    {
        return $this->idCota;
    }

    /**
     * @param FrigaEditalCargo $idCota
     * @return FrigaInscricao
     */
    public function setIdCota($idCota)
    {
        $this->idCota = $idCota;
        return $this;
    }

    /**
     * @return string
     */
    public function getMatriculaNro()
    {
        return $this->matriculaNro;
    }

    /**
     * @param string $matriculaNro
     * @return FrigaInscricao
     */
    public function setMatriculaNro($matriculaNro)
    {
        $this->matriculaNro = $matriculaNro;
        return $this;
    }

    /**
     * @return string
     */
    public function getMatriculaCurso()
    {
        return $this->matriculaCurso;
    }

    /**
     * @param string $matriculaCurso
     * @return FrigaInscricao
     */
    public function setMatriculaCurso($matriculaCurso)
    {
        $this->matriculaCurso = $matriculaCurso;
        return $this;
    }

    /**
     * @return string
     */
    public function getMatriculaIndiceDesempenho()
    {
        return $this->matriculaIndiceDesempenho;
    }

    /**
     * @param string $matriculaIndiceDesempenho
     * @return FrigaInscricao
     */
    public function setMatriculaIndiceDesempenho($matriculaIndiceDesempenho)
    {
        $this->matriculaIndiceDesempenho = $matriculaIndiceDesempenho;
        return $this;
    }

    /**
     * @return integer
     */
    public function getMatriculaBeneficio()
    {
        return $this->matriculaBeneficio;
    }

    /**
     * @param integer $matriculaBeneficio
     * @return FrigaInscricao
     */
    public function setMatriculaBeneficio($matriculaBeneficio)
    {
        $this->matriculaBeneficio = $matriculaBeneficio;
        return $this;
    }


    /**
     * @return string
     */
    public function getRecebimentoBolsa()
    {
        return $this->recebimentoBolsa;
    }

    /**
     * @param string $recebimentoBolsa
     * @return FrigaInscricao
     */
    public function setRecebimentoBolsa($recebimentoBolsa)
    {
        $this->recebimentoBolsa = $recebimentoBolsa;
        return $this;
    }


    /**
     * @return string
     */
    public function getRneDataValidade()
    {
        return $this->rneDataValidade;
    }

    /**
     * @param string $rneDataValidade
     * @return FrigaInscricao
     */
    public function setRneDataValidade($rneDataValidade)
    {
        $this->rneDataValidade = $rneDataValidade;
        return $this;
    }


    /**
     * Add idArquivo
     *
     * @param FrigaArquivo $idArquivo
     *
     * @return FrigaInscricao
     */
    public function addIdArquivo(FrigaArquivo $idArquivo)
    {
        $this->idArquivo[] = $idArquivo;

        return $this;
    }

    /**
     * Remove idArquivo
     *
     * @param FrigaArquivo $idArquivo
     */
    public function removeIdArquivo(FrigaArquivo $idArquivo)
    {
        $this->idArquivo->removeElement($idArquivo);
    }

    /**
     * Get idArquivo
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdArquivo()
    {
        return $this->idArquivo;
    }

    /**
     * @return int
     */
    public function getSexo()
    {
        return $this->sexo;
    }

    /**
     * @param int $sexo
     * @return FrigaInscricao
     */
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;
        return $this;
    }

    /**
     * @return string
     */
    public function getProjetoTitulo()
    {
        return $this->projetoTitulo;
    }

    /**
     * @param string $projetoTitulo
     * @return FrigaInscricao
     */
    public function setProjetoTitulo($projetoTitulo)
    {
        $this->projetoTitulo = $projetoTitulo;
        return $this;
    }

    /**
     * @return string
     */
    public function getProjetoResumo()
    {
        return $this->projetoResumo;
    }

    /**
     * @param string $projetoResumo
     * @return FrigaInscricao
     */
    public function setProjetoResumo($projetoResumo)
    {
        $this->projetoResumo = $projetoResumo;
        return $this;
    }

    /**
     * @return string
     */
    public function getProjetoAreaConhecimento()
    {
        return $this->projetoAreaConhecimento;
    }

    /**
     * @param string $projetoAreaConhecimento
     * @return FrigaInscricao
     */
    public function setProjetoAreaConhecimento($projetoAreaConhecimento)
    {
        $this->projetoAreaConhecimento = $projetoAreaConhecimento;
        return $this;
    }



    /**
     * @return ArrayCollection
     */
    public function getPontuacao()
    {
        return $this->pontuacao;
    }

    /**
     * @return FrigaInscricaoPontuacao|null
     */
    public function getPontuacaoItem($id)
    {
        /** @var FrigaInscricaoPontuacao $pt */
        foreach ($this->pontuacao as $pt) {
            if ($pt->getIdEditalPontuacao()->getId() == $id) {
                return $pt;
            }
        };
        return null;
    }

    /**
     * @param FrigaEditalPontuacao $item
     * @return ArrayCollection
     */
    public function getPontuacaoAvaliacaoItem(FrigaEditalPontuacao $item)
    {
        $pt = $this->avaliacao->filter(function (FrigaInscricaoPontuacaoAvaliacao $a) use ($item) {
            if ($a->getIdEditalPontuacao()) {
                return $a->getIdEditalPontuacao()->getId() == $item->getId();
            } else {
                return $a->getIdEditalPontuacaoCategoria()->getId() == $item->getIdCategoria()->getId();
            }
        });
        return $pt;
    }

    /**
     * @param FrigaEditalPontuacao $item
     * @return int|string|null
     */
    public function getPontuacaoAvaliacaoItemValor(FrigaEditalPontuacao $item, $divulgacao = false)
    {
        $pt = $this->getPontuacaoAvaliacaoItem($item);
        $tmp = 0;
        if ($pt->count() == 1) {
            $tmp = $pt->first()->getValorAvaliador() + 0;
        } elseif ($pt->count() > 1) {
            foreach ($pt as $p) {
                $tmp = bcadd($tmp, $p->getValorAvaliador(), 5);
            }
            $tmp = bcdiv($tmp, $pt->count(), 5);
        }

        return $tmp;
    }


    /**
     * @return FrigaInscricaoPontuacao|null
     */
    public function getPontuacaoCategoriaItem($id)
    {
        /** @var FrigaInscricaoPontuacao $pt */
        foreach ($this->pontuacao as $pt) {
            if ($pt->getIdEditalPontuacao()->getIdCategoria()) {
                if ($pt->getIdEditalPontuacao()->getIdCategoria()->getId() == $id) {
                    return $pt;
                }
            }
        };
        return null;
    }

    /**
     * @return \stdClass
     */
    public function getObjsituacao()
    {
        $obj = new \stdClass();
        $obj->id = $this->idSituacao;
        $obj->descricao = "";
        $obj->icone = "";
        $obj->css = "label label-info";
        $obj->cssAlert = "alert alert-info";
        switch ($this->idSituacao) {
            case -999:
                $obj->descricao = "Inscrição Cancelada";
                $obj->css = "label label-dark";
                $obj->icone = "fa fa-clock-o";
                $obj->cssAlert = "alert alert-dark";
                break;
            case 0:
                $obj->descricao = "Inscrição Realizada";
                $obj->css = "label label-success";
                $obj->icone = "fa fa-clock-o";
                $obj->cssAlert = "alert alert-success";
                break;
            case 1:
                $obj->descricao = "Inscrição Não Homologada";
                $obj->css = "label label-danger";
                $obj->icone = "fa fa-times";
                $obj->cssAlert = "alert alert-danger";
                break;
            case 2:
                $obj->descricao = "Inscrição Homologada";
                $obj->css = "label label-info";
                $obj->icone = "fa fa-clock-o";
                $obj->cssAlert = "alert alert-success";
                break;
            case 3:
                $obj->descricao = "Desclassificado";
                $obj->css = "label label-danger";
                $obj->icone = "fa fa-clock-o";
                $obj->cssAlert = "alert alert-danger";
                break;
            case 4:
                $obj->descricao = "Em avaliação";
                $obj->css = "label label-danger";
                $obj->icone = "fa fa-clock-o";
                $obj->cssAlert = "alert alert-danger";
                break;
            case 5:
                $obj->descricao = "Aguardando Recurso";
                $obj->css = "label label-warning";
                $obj->icone = "fa fa-clock-o";
                $obj->cssAlert = "alert alert-warning";
                break;
            case 6:
                $obj->descricao = "Classificado";
                $obj->css = "label label-success";
                $obj->icone = "fa fa-clock-o";
                $obj->cssAlert = "alert alert-success";
                break;
            case 7:
                $obj->descricao = "Convocado";
                $obj->css = "label label-success";
                $obj->icone = "fa fa-clock-o";
                $obj->cssAlert = "alert alert-success";
                break;
        }
        return $obj;
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
    public function getClassificacao()
    {
        return $this->classificacao;
    }

    /**
     * @return ArrayCollection
     */
    public function getRecursoEtapa()
    {
        return $this->idEdital->getEtapa()->filter(function (FrigaEditalEtapa $e) {
            return $e->getTipo() == 6;
        });
    }

    /**
     * @return ArrayCollection
     */
    public function getIdProjetoParticipante()
    {
        return $this->idProjetoParticipante;
    }


    /**
     * @param $id
     * @return FrigaInscricaoRecurso
     */
    public function getRecurso($id)
    {
        return $this->getRecursos()->filter(function (FrigaInscricaoRecurso $r) use ($id) {
            return $r->getId() == $id;
        })->first();
    }

    public function getRecursoArquivo($id)
    {
        $tmp = new ArrayCollection();
        $recurso = $this->getRecurso($id);
        if ($recurso) {
            $tmp = $recurso->getIdArquivo();
        }
        return $tmp;

    }

    /**
     * @param FrigaEditalEtapa|null $etapa
     * @return ArrayCollection
     */
    public function getRecursosEtapa($etapa)
    {
        //dump($this->recursos);
        return $this->recursos->filter(function (FrigaInscricaoRecurso $r) use ($etapa) {
            return !is_null($etapa) and $r->getIdEditalEtapa()->getId() == $etapa->getId();
        });
    }


    /**
     * @param FrigaEditalEtapa $etapa
     * @return ArrayCollection
     */
    public function getConvocacaoEtapa(FrigaEditalEtapa $etapa)
    {
        return $this->convocacao->filter(function (FrigaConvocacao $c) use ($etapa) {
            return $c->getIdEtapa()->getId() == $etapa->getId();
        });
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
    public function getRecursos()
    {
        return $this->recursos;
    }

    /**
     * @return ArrayCollection
     */
    public function getFeedback()
    {
        return $this->feedback;
    }

    public function getFeedbackEtapa(FrigaEditalEtapa $etapa)
    {
        return $this->feedback->filter(function (FrigaInscricaoFeedback $f) use ($etapa) {
            return $etapa->getId() == $f->getIdEtapa()->getId();
        });
    }


    /**
     * @param bool $avaliacao
     * @param FrigaEditalPontuacaoCategoria|null $peso
     * @param boolean $excedente
     * @param FrigaEditalEtapa|null $etapa
     * @return int|string
     */
    public function getPontuacaoSoma($avaliacao = false, FrigaEditalPontuacaoCategoria $peso = null, $excedente = false, FrigaEditalEtapa $etapa = null)
    {
        //dump($peso);
        $total = 0;
        $totalExcedente = 0;
        if ($this->idSituacao == 1 or $this->idSituacao == 3) {
            return 0;
        }
        if ($peso) {
            $root = new ArrayCollection();
            $root->add($peso);
        } else {
            $root = $this->getIdEdital()->getPontuacaoCategoria()
                ->filter(function (FrigaEditalPontuacaoCategoria $c) use ($peso) {
                    return $c->getIdCategoria() == null;
                });
        }
        /** @var FrigaEditalPontuacaoCategoria $peso */
        foreach ($root as $peso) {
            $pontuacaoCategoria = 0;
            $pontuacaoCategoriaExcedente = 0;
            /** @var FrigaEditalPontuacaoCategoria $categoria */
            foreach ($peso->getFilhos() as $categoria) {
                $pontuacaoItem = 0;
                /** @var FrigaEditalPontuacao $item */
                foreach ($categoria->getPontuacao() as $item) {
                    //Se $avalicao == true então capturar a nota do avaliador
                    //Se $avalicao == false então capturar a nota informada pelo candidato
                    if ($avaliacao) {
                        $pontuacaoAvaliacao = $this->avaliacao
                            ->filter(function (FrigaInscricaoPontuacaoAvaliacao $avaliacao) use ($item) {

                                return $avaliacao->getIdEditalPontuacao() != null
                                    and $avaliacao->getIdEditalPontuacao()->getId() == $item->getId();
                            });
                        // se $pontuacaoAvaliacao == 1, pontuação avaliada pelo coletivo
                        // se $pontuacaoAvaliacao  > 1, pontuacao avaliada individualmente
                        if ($pontuacaoAvaliacao->count() == 1) {
                            if ($item->getPontuacaoTipo() > 0) {
                                //Verifica se o anexo foi aceito
                                if ($pontuacaoAvaliacao->first()->isConsiderado()) {
                                    $pontuacaoItem = bcadd($pontuacaoItem, $pontuacaoAvaliacao->first()->getValorAvaliador(), 5);
                                }
                            } else {
                                $pontuacaoItem = bcadd($pontuacaoItem, $pontuacaoAvaliacao->first()->getValorAvaliador(), 5);
                            }
                        } elseif ($pontuacaoAvaliacao->count() > 1) {
                            $tmp = 0;
                            /** @var FrigaInscricaoPontuacaoAvaliacao $a */
                            foreach ($pontuacaoAvaliacao as $a) {
                                $tmp = bcadd($tmp, $a->getValorAvaliador(), 5);
                            }
                            $pontuacaoItem = bcadd($pontuacaoItem, bcdiv($tmp, $pontuacaoAvaliacao->count(), 5), 5);
                        }
                    } else {
                        if ($this->getPontuacaoItem($item->getId())) {
                            $pontuacaoItem = bcadd($pontuacaoItem, $this->getPontuacaoItem($item->getId())->getValorInformado(), 5);
                        }
                    }
                }
                if ($pontuacaoItem > $categoria->getValorMaximo()) {
                    $pontuacaoCategoria = bcadd($pontuacaoCategoria, $categoria->getValorMaximo(), 5);
                    $tmp = bcsub($pontuacaoItem, $categoria->getValorMaximo(), 5);
                    $pontuacaoCategoriaExcedente = bcadd($pontuacaoCategoriaExcedente, $tmp, 5);
                } else {
                    $pontuacaoCategoria = bcadd($pontuacaoCategoria, $pontuacaoItem, 5);
                }
            }
            $total = bcadd($total, bcmul($peso->getValorMaximo(), $pontuacaoCategoria, 5), 5);
            $totalExcedente = bcadd($totalExcedente, bcmul($peso->getValorMaximo(), $pontuacaoCategoriaExcedente, 5), 5);
        }

        return $excedente ? floatval($totalExcedente) : floatval($total);
    }

    /**
     * @param bool $avaliacao
     * @param FrigaEditalPontuacaoCategoria|null $categoria
     * @param bool $excedente
     * @param FrigaEditalEtapa|null $etapa
     * @return float|int
     */
    public function getPontuacaoSomaCategoria($avaliacao = false, FrigaEditalPontuacaoCategoria $categoria = null, $excedente = false, FrigaEditalEtapa $etapa = null)
    {
        $total = 0;
        $totalExcedente = 0;
        if ($this->idSituacao == 1 or $this->idSituacao == 3) {
            return 0;
        }
        $peso = $categoria->getIdCategoria();
        $pontuacaoCategoria = 0;
        $pontuacaoCategoriaExcedente = 0;
        $pontuacaoItem = 0;
        /** @var FrigaEditalPontuacao $item */
        foreach ($categoria->getPontuacao() as $item) {
            //Se $avalicao == true então capturar a nota do avaliador
            //Se $avalicao == false então capturar a nota informada pelo candidato
            if ($avaliacao) {
                $pontuacaoAvaliacao = $this->avaliacao
                    ->filter(function (FrigaInscricaoPontuacaoAvaliacao $avaliacao) use ($item) {

                        return $avaliacao->getIdEditalPontuacao() != null
                            and $avaliacao->getIdEditalPontuacao()->getId() == $item->getId();
                    });
                // se $pontuacaoAvaliacao == 1, pontuação avaliada pelo coletivo
                // se $pontuacaoAvaliacao  > 1, pontuacao avaliada individualmente
                if ($pontuacaoAvaliacao->count() == 1) {
                    //Verifica se o anexo foi aceito
                    if ($pontuacaoAvaliacao->first()->isConsiderado()) {
                        $pontuacaoItem = bcadd($pontuacaoItem, $pontuacaoAvaliacao->first()->getValorAvaliador(), 5);
                    }
                } elseif ($pontuacaoAvaliacao->count() > 1) {
                    $tmp = 0;
                    /** @var FrigaInscricaoPontuacaoAvaliacao $a */
                    foreach ($pontuacaoAvaliacao as $a) {
                        $tmp = bcadd($tmp, $a->getValorAvaliador(), 5);
                    }
                    $pontuacaoItem = bcadd($pontuacaoItem, bcdiv($tmp, $pontuacaoAvaliacao->count(), 5), 5);
                }
            } else {
                if ($this->getPontuacaoItem($item->getId())) {
                    $pontuacaoItem = bcadd($pontuacaoItem, $this->getPontuacaoItem($item->getId())->getValorInformado(), 5);
                }
            }
        }
        if ($pontuacaoItem > $categoria->getValorMaximo()) {
            $pontuacaoCategoria = bcadd($pontuacaoCategoria, $categoria->getValorMaximo(), 5);
            $tmp = bcsub($pontuacaoItem, $categoria->getValorMaximo(), 5);
            $pontuacaoCategoriaExcedente = bcadd($pontuacaoCategoriaExcedente, $tmp, 5);
        } else {
            $pontuacaoCategoria = bcadd($pontuacaoCategoria, $pontuacaoItem, 5);
        }

        $total = bcadd($total, bcmul($peso->getValorMaximo(), $pontuacaoCategoria, 5), 5);
        $totalExcedente = bcadd($totalExcedente, bcmul($peso->getValorMaximo(), $pontuacaoCategoriaExcedente, 5), 5);

        return $excedente ? floatval($totalExcedente) : floatval($total);
    }


    /***
     * Retorna a pontuação de um avaliador numa detemrinada etapa
     *
     * @param Usuario $avaliador
     * @param FrigaEditalEtapa $etapa
     * @return ArrayCollection
     */
    public function getAvalicaoAvaliador(Usuario $avaliador, FrigaEditalEtapa $etapa)
    {
        return $this->avaliacao->filter(
            function (FrigaInscricaoPontuacaoAvaliacao $avalicao) use ($avaliador, $etapa) {
                return $avalicao->getIdAvaliador()->getId() == $avaliador->getId() and
                    $avalicao->getIdEtapa() == $etapa->getId();
            });
    }


    /**
     * @ORM\PreUpdate
     *
     * @param PreUpdateEventArgs $args
     * @throws \Exception
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        if ($args->hasChangedField('registroDataCriacao')) {
            $this->setRegistroDataCriacao($args->getOldValue('registroDataCriacao'));
        }
        $this->setRegistroDataAtualizacao(new \DateTime());
    }

    /**
     * @ORM\PrePersist
     */
    public function PrePersist()
    {
        $this->uuid = uniqid();
        $this->setRegistroDataCriacao(new \DateTime());
        $this->setRegistroDataAtualizacao(new \DateTime());
    }
}
