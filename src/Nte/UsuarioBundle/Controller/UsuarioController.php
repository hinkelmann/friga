<?php

namespace Nte\UsuarioBundle\Controller;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Nte\Aplicacao\CadastrosBundle\Entity\FrigaEdital;
use Nte\Aplicacao\CadastrosBundle\Entity\FrigaPessoa;
use Nte\Aplicacao\CadastrosBundle\Entity\FrigaRecurso;
use Nte\UsuarioBundle\Entity\Usuario;
use Nte\UsuarioBundle\Form\PrimeiroAcessoType;
use Nte\UsuarioBundle\Form\UsuarioComumType;
use Nte\UsuarioBundle\Form\UsuarioType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Query\Expr;

class UsuarioController extends Controller
{
    /**
     * Index dos usuários
     * @return Response
     */
    public function indexAction(Request $request)
    {


        $em = $this->getDoctrine()->getManager();

        $obj = new \stdClass();
        $obj->paginaTotal = 1;
        $obj->paginaAtual = $request->query->get('p') ?? 1;
        $obj->paginaItens = $request->query->get('r')??10;
        $obj->paginacao = [];
        $obj->itens = [];
        $obj->itensTotal = 0;
        $obj->ordem = $request->query->get('ord') ?? false;
        $obj->busca0 = $request->query->get('b0') ?? false;
        $obj->busca1 = $request->query->get('b1') ?? false;


        /** @var QueryBuilder $usuario */
        $usuario = $em->createQueryBuilder();
        $usuario->select('u')->from(Usuario::class, 'u');

        if ($obj->ordem) {
            $usuario->orderBy('u.nome', $obj->ordem == "DESC" ? "DESC" : "ASC");
        } else {
            $usuario->orderBy('u.nome', 'ASC');

        }
        if ($obj->busca0) {
            $obj->busca0 = str_replace(" ", "%",$obj->busca0);
            $usuario
                ->andWhere('LOWER(u.nome) LIKE  :busca or LOWER(u.email) LIKE  :busca')
                ->setParameter('busca', "%" . strtolower($obj->busca0) . "%");
            $obj->busca0 = str_replace("%", " ",$obj->busca0);
        }
        if ($obj->busca1) {
            $usuario
                ->andWhere('LOWER(u.nome) LIKE  :busca')
                ->setParameter('busca', strtolower($obj->busca1) . "%");
        }

        $obj->paginacao = new Paginator($usuario->getQuery());
        $obj->itensTotal = $obj->paginacao->count();
        $obj->itensInicio = $obj->paginaItens * ($obj->paginaAtual - 1);
        $obj->paginaTotal = ceil($obj->paginacao->count() / $obj->paginaItens);
        $obj->itens = new ArrayCollection($obj->paginacao
            ->getQuery()
            ->setFirstResult($obj->itensInicio)
            ->setMaxResults($obj->paginaItens)
            ->getResult());

        return $this->render('NteUsuarioBundle:Usuario:index.html.twig', [
            'dataset' => $obj,
        ]);
    }

    /**
     * Listar usuários
     * @return Response
     */
    public function listaAction()
    {
        $em = $this->getDoctrine()->getManager();
        $u = $em->getRepository(Usuario::class)
            ->findby(['enabled' => 1], ['lastLogin' => 'desc'], 15);


        return $this->render('NteUsuarioBundle:Usuario:lista.html.twig', ['usuarios' => $u]);
    }

    /**
     * Visualizar Usuário
     * @param Usuario $usuario
     * @return Response
     */
    public function perfilAction(Usuario $usuario)
    {
        if (!$usuario) {
            $usuario = $this->getUser();
        }
        return $this->render('@NteUsuario/Usuario/perfil.html.twig', [
            'usuario' => $usuario,
        ]);
    }

    /**
     * Editar Usuário
     * @param Request $request
     * @param Usuario $usuario
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function ativarAction(Request $request, Usuario $usuario)
    {
        if ($this->isGranted('ROLE_ADMIN_USER')) {
            $usuario->setEnabled(!$usuario->isEnabled());
            $em = $this->getDoctrine()->getManager();
            $em->persist($usuario);
            $em->flush();
            return $this->redirect($this->generateUrl('nte_usuario_index'));
        } else {
            return $this->redirectToRoute('nte_usuario_perfil', ['id' => $usuario->getId()]);
        }
    }

    /**
     * Editar Usuário
     * @param Request $request
     * @param Usuario $usuario
     * @return RedirectResponse|Response
     */
    public function editarAction(Request $request, Usuario $usuario)
    {
        if ($this->isGranted('ROLE_ADMIN_USER') or $usuario->getId() == $this->getUser()->getId()) {
            if ($this->isGranted('ROLE_ADMIN_USER')) {
                $form = $this->createForm(UsuarioType::class, $usuario);
            } else {
                $form = $this->createForm(UsuarioComumType::class, $usuario);
            }
            $form->handleRequest($request);
            if ($form->isValid() && $form->isSubmitted()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($usuario);
                $em->flush();
                return $this->redirect($this->generateUrl('nte_usuario_index'));
            }
            return $this->render('NteUsuarioBundle:Usuario:form.html.twig', array(
                'entity' => $usuario,
                'form' => $form->createView(),
            ));
        } else {
            return $this->redirectToRoute('nte_usuario_perfil', ['id' => $usuario->getId()]);
        }
    }

    /**
     * Editar Usuário
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editarPrimeiroAcessoAction(Request $request)
    {
        $usuario = $this->getUser();
        $form = $this->createForm(PrimeiroAcessoType::class, $usuario);
        $form->handleRequest($request);
        if ($form->isValid() && $form->isSubmitted()) {
            // $usuario->setPlainPassword($form->get('plainPassword')->getData());
            $usuario->setEnabled(TRUE);
            $em = $this->getDoctrine()->getManager();
            $em->persist($usuario);
            $em->flush();
            return $this->redirect($this->generateUrl('nte_admin_painel_homepage'));
        }

        return $this->render('NteUsuarioBundle:Usuario:form-primeiro-acesso.html.twig', array(
            'entity' => $usuario,
            'form' => $form->createView(),
        ));
    }


    public function novoAction(Request $request)
    {
        $entity = new Usuario();
        $form = $this->createForm(UsuarioType::class, $entity);
        $form->handleRequest($request);
        if ($form->isValid() && $form->isSubmitted()) {
            $entity->setPlainPassword($form->get('plainPassword')->getData());
            $entity->setEnabled(TRUE);
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('nte_usuario_index'));
        }
        return $this->render('NteUsuarioBundle:Usuario:form.html.twig', [
                'entity' => $entity,
                'form' => $form->createView(),
            ]
        );
    }

    public function uploadUserImgAction(Request $request)
    {

    }


    private function createEditForm(Usuario $entity)
    {
        $form = $this->createForm(RegistrationFormType::class, $entity, [
            'action' => $this->generateUrl('nte_usuario_update', ['id' => $entity->getId()]),
            'method' => 'PUT',
            'attr' => ['class' => 'form-horizontal']
        ]);
        return $form;
    }

    public function testeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $edital = $em->find(FrigaEdital::class, 1);

        //->format('%H:%i'));

        $dif = date_diff(new \DateTime(), $edital->getHomologacaoEncerramento());

        $h = $dif->format("%H");
        $i = $dif->format("%i");


        /*  $entity = $em->getRepository(Usuario::class)->findAll();

          foreach ($entity as $u) {

              $m = "Prezado(a)  {$u->getNome()} ,<br>"
                  . "O prazo final da fase de homologação se encerrerá em <b> $h </b> horas e <b>$i</b> minutos.<br>"
                  . "Se você ainda não homologou ou precise corrigir a homologação de algum candidato, faça o mais breve possível.<br>"
                  . "É obrigatório que inscrição de cada candidato esteja homologado para que o mesmo seja avaliado na próxima etapa. <br>"
                  . "Não deixe para última hora!!!<br><br><br>"
                  //. "Se você estiver enfrentando algum problema, entrar em contato imediatamente!!<br><br>"
                  . "*<b>Lembre-se</b> que após o término do período de homologação não será possível alterar a situação das inscrições dos candidatos.<br> "
                  . "Informações sobre o processo de homologação podem ser encontradas no manual do sistema, "
                  . "disponível em http://assistentepolo.nte.ufsm.br/app/suporte/manual <br>"
                  . "Em caso de dúvidas, entrar em contato o mais breve possível.<br><br>"
                  . "Esta é uma mensagem automática do sistema.<br>";
              echo $m;
              $message = \Swift_Message::newInstance()
                  ->setSubject("AVISO - O prazo de homologação está se esgotando!")
                  ->setFrom('assistentepolo@cead.ufsm.br', "Não Responder")
                  ->setTo($u->getEmail())
                  //->setBcc(['alexandre@nte.ufsm.br', 'luizguilherme@cead.ufsm.br'])
                  //->setBcc(['luizguilherme@cead.ufsm.br'])
                  ->setBody($m, 'text/html');
              $this->get('mailer')->send($message);

          }

  */
        /*

                $em = $this->getDoctrine()->getManager();
                $entity = $em->getRepository(Usuario::class)->findAll();
                foreach ($entity as $u) {
                    $pessoas = $this->getDoctrine()
                        ->getManager()
                        ->createQueryBuilder()
                        ->select('fp')
                        ->from(FrigaPessoa::class, 'fp')
                        ->where('fp.idSituacao <>  -99 or fp.idSituacao is null')
                        ->andWhere('fp.idPolo in (:polo)')
                        ->setParameter('polo', $u->getPolos())
                        ->getQuery()->getResult();
                    $a = count($pessoas);

                    $b = count($this->getDoctrine()
                        ->getManager()
                        ->createQueryBuilder()
                        ->select('fp')
                        ->from(FrigaPessoa::class, 'fp')
                        ->where('fp.idSituacao = 1 or fp.idSituacao = -1 or fp.idSituacao = -10')
                        ->andWhere('fp.idPolo in (:polo)')
                        ->setParameter('polo', $u->getPolos())
                        ->getQuery()->getResult());

                    if($a >= 1 && $b ==0 && $u->getLastLogin() ==null){
                     //   dump($u);
                        $message = \Swift_Message::newInstance()
                            ->setSubject("URGENTE - Entrar em contato!!!")
                            ->setFrom('assistentepolo@cead.ufsm.br', "Não Responder")
                            ->setTo($u->getEmail())
        //            ->setBcc(['alexandre@nte.ufsm.br', 'luizguilherme@cead.ufsm.br'])
                            //->setBcc(['luizguilherme@cead.ufsm.br'])
                            ->setBody("Prezado  Usuário,<br>"
                                . "Identificamos que você nunca acessou o sistema de homologação <br>"
                                . "Se você estiver enfrentando algum problema, entrar em contato imediatamente!!<br><br>"
                                . "Nas próximas 23h o sistema encerrará o processo de homologação.<br>"
                                . "Se você ainda não homologou ou precise corrigir a homologação de algum candidato, faça o mais breve possível. "
                                . "Lembre-se que após o término do período de homologação não será possível alterar a situação das inscrições dos candidatos.<br> "
                                . "Informações sobre o processo de homologação podem ser encontradas no manual do sistema, "
                                . "disponível em http://assistentepolo.nte.ufsm.br/app/suporte/manual <br>"
                                . "Em caso de dúvidas, entrar em contato o mais breve possível.<br><br>"
                                . "Não deixe para última hora!!!<br><br>"
                                . "Esta é uma mensagem automática do sistema.<br>", 'text/html');
                        $this->get('mailer')->send($message);
                    }
                    if($a >= 1 && $b ==0 && $u->getLastLogin() ){
                     //   dump($u);
                        $message = \Swift_Message::newInstance()
                            ->setSubject("URGENTE - Entrar em contato!!!")
                            ->setFrom('assistentepolo@cead.ufsm.br', "Não Responder")
                            ->setTo($u->getEmail())
        //            ->setBcc(['alexandre@nte.ufsm.br', 'luizguilherme@cead.ufsm.br'])
                            //->setBcc(['luizguilherme@cead.ufsm.br'])
                            ->setBody("Prezado  Usuário,<br>"
                                . "Identificamos que você nunca acessou o ambiente porém não homologou nenhuma inscrição <br>"
                                . "Se você estiver enfrentando algum problema, entrar em contato imediatamente!!<br><br>"
                                . "Nas próximas 23h o sistema encerrará o processo de homologação.<br>"
                                . "Se você ainda não homologou ou precise corrigir a homologação de algum candidato, faça o mais breve possível. "
                                . "Lembre-se que após o término do período de homologação não será possível alterar a situação das inscrições dos candidatos.<br> "
                                . "Informações sobre o processo de homologação podem ser encontradas no manual do sistema, "
                                . "disponível em http://assistentepolo.nte.ufsm.br/app/suporte/manual <br>"
                                . "Em caso de dúvidas, entrar em contato o mais breve possível.<br><br>"
                                . "Não deixe para última hora!!!<br><br>"
                                . "Esta é uma mensagem automática do sistema.<br>", 'text/html');
                        $this->get('mailer')->send($message);
                    }
                    */


        /*
          $message = \Swift_Message::newInstance()
              ->setSubject("Atenção - O prazo para homologação terminará nas próximas 23h !!!")
              ->setFrom('assistentepolo@cead.ufsm.br', "Não Responder")
              ->setTo($u->getEmail())
  //            ->setBcc(['alexandre@nte.ufsm.br', 'luizguilherme@cead.ufsm.br'])
              //->setBcc(['luizguilherme@cead.ufsm.br'])
              ->setBody("Prezado  Usuário,<br>"
                  . "Nas próximas 23h o sistema encerrará o processo de homologação.<br>"
                  . "Se você ainda não homologou ou precise corrigir a homologação de algum candidato, faça o mais breve possível. "
                  . "Lembre-se que após o término do período de homologação não será possível alterar a situação das inscrições dos candidatos.<br> "
                  . "Informações sobre o processo de homologação podem ser encontradas no manual do sistema, "
                  . "disponível em http://assistentepolo.nte.ufsm.br/app/suporte/manual <br>"
                  . "Em caso de dúvidas, entrar em contato o mais breve possível.<br><br>"
                  . "Não deixe para última hora!!!<br><br>"
                  . "Esta é uma mensagem automática do sistema.<br>", 'text/html');
          $this->get('mailer')->send($message);

      }
   */
        return new Response();
    }


    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('NteUsuarioBundle:Usuario')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Destino entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $userManager = $this->container->get('fos_user.user_manager');
            $userManager->updateUser($editForm->getData());
            return $this->redirect($this->generateUrl('nte_usuario_perfil', array('id' => $id)));
        }

        return $this->render('NteUsuarioBundle:Usuario:perfil.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'action' => $this->generateUrl('nte_usuario_update', ['id' => $id]),

        ));
    }

}


