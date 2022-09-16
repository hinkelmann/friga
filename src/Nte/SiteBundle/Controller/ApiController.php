<?php
namespace Nte\SiteBundle\Controller;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends FOSRestController
{
    /**
     * @return Response
     */
    public function getDemosAction()
    {
        $data = $this->getUser();
        $view = $this->view($data);
        return $this->handleView($view);
    }
    public function postDemosAction(Request $request)
    {
        $data = $request->request;
        $view = $this->view($data);
        return $this->handleView($view);
    }

    public function getUsersAction(){
        $data = $this->getUser();
        $view = $this->view($data);
        return $this->handleView($view);
    }

    public function postUsersAction(){
        $data = $this->getUser();
        return $this->handleView($this->view($data));
    }
}