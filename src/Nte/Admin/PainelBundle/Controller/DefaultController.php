<?php

namespace Nte\Admin\PainelBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario;
use Nte\UsuarioBundle\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $dados = [];
	    $obj = new \stdClass();
	    $obj->candidatos = 0;
	    $obj->avaliados =0;
	    $obj->entrevistados = 0;
	    $obj->recursos = 0;
        return $this->render('NteAdminPainelBundle:Default:index.html.twig', [
            'dados'             => $dados,
            'graficoAlteracoes' => "",
            'avaliador'         => $obj,
            'editais'            => $this->editais()
        ]);
    }

    /**
     * Traaz todos os editais que usuário pode ter acesso;
     * Se administrador, traz todos os editais.
     *
     * @param int $situacao
     * @return array|ArrayCollection|object[]
     */
    public function editais($situacao = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $editais = new ArrayCollection();
        if ($this->isGranted('ROLE_ADMIN')) {
            $editais = $em->getRepository(FrigaEdital::class)->findAll(['situacao' => $situacao]);
        } else if ($this->isGranted('ROLE_AVALIADOR')) {

            /** @var ArrayCollection $tmp */
            $tmp = $this->getUser()
                ->getIdEditalUsuario()
                ->filter(function (FrigaEditalUsuario $eu) use ($situacao) {
                    return $eu->getIdEdital()->getSituacao() == $situacao;
                });
            if ($tmp->count()) {
                /** @var FrigaEditalUsuario $eu */
                foreach ($tmp as $eu) {
                    if (!$editais->contains($eu->getIdEdital())) {
                        $editais->add($eu->getIdEdital());
                    }
                };
                $editais = $editais->filter(function (FrigaEdital $edital) {
                    return $edital->getEtapa()->filter(function (FrigaEditalEtapa $etapa) {
                        return $etapa->getPeriodoHabilitado();
                    })->count();
                });
            }
        }
        return $editais;
    }

    /**
     * @return \stdClass
     */
    private function visaoAvaliador(){

        $obj = new \stdClass();
        $obj->candidatos = $a;
        $obj->avaliados =$b;
        $obj->entrevistados = $c;
        $obj->recursos = $d;

        return $obj;
    }

    public function enviarMensagemAction(Request $request){


        if($request->request->get('token')){
            if($this->isCsrfTokenValid('mensagem-para-todos',$request->request->get('token'))){
                $i = 0;
                $lista=[];
                if($request->request->get('destinatario') == "SEM_REGRA"){
                    $pessoas = $this->getDoctrine()
                        ->getManager()
                        ->createQueryBuilder()
                        ->select('fp')
                        ->from(FrigaPessoa::class, 'fp')
                        ->where('fp.idSituacao <>  -99 or fp.idSituacao is null')
                        ->getQuery()->getResult();
                    foreach ($pessoas as $p){
                        $this->enviarEmail($p->getContatoEmail(), $request->request->get('assunto'),$request->request->get('msg'));
                        $i++;
                        $lista[$p->getNome()]= $p->getContatoEmail();
                    }
                }else{
                    $pessoas = $this->getDoctrine()
                        ->getManager()
                        ->createQueryBuilder()
                        ->select('u')
                        ->from(Usuario::class, 'u')
                        ->where('u.roles LIKE :role')
                        ->setParameter('role', "%".$request->request->get('destinatario')."%")
                        ->getQuery()->getResult();
                    foreach ($pessoas as $p){
                        $this->enviarEmail($p->getEmail(), $request->request->get('assunto'),$request->request->get('msg'));
                        $i++;
                        $lista[$p->getNome()] =$p->getEmail();
                    }
                }
                return $this->render('NteAdminPainelBundle:Default:mensagem.html.twig', [
                    "msgTotal"=>$i,
                    "lista"=>$lista
                ]);
            }
        }
        return $this->render('NteAdminPainelBundle:Default:mensagem.html.twig', []);
    }

    public function enviarEmail($destinatario,$assunto,$msg){

        $message = \Swift_Message::newInstance()
            ->setSubject($assunto)
            ->setFrom('assistentepolo@cead.ufsm.br',"Não Responder")
            ->setTo($destinatario)
            //->setBcc(['alexandre@nte.ufsm.br', 'luizguilherme@cead.ufsm.br'])
            // ->setBcc(['luizguilherme@cead.ufsm.br'])
            ->setBody($msg, 'text/html');
        $this->get('mailer')->send($message);

    }
}
