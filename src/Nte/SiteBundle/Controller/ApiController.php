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

namespace Nte\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractFOSRestController
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

    public function getUsersAction()
    {
        $data = $this->getUser();
        $view = $this->view($data);

        return $this->handleView($view);
    }

    public function postUsersAction()
    {
        $data = $this->getUser();

        return $this->handleView($this->view($data));
    }
}
