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

namespace Nte\SiteBundle\Model;

use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;

/**
 * @SWG\Definition(
 *     definition="Edital",
 *     type="object",
 *     required={"title"}
 * )
 */
class Edital
{
    /**
     * @var string|null
     *
     * @Serializer\Groups({"public"})
     *
     * @SWG\Property(type="string", maxLength=255,description="The unique identifier of the edital.")
     */
    public $uuid;

    /**
     * @var string|null
     *
     * @Serializer\Groups({"public"})
     *
     * @SWG\Property(type="string", maxLength=255, description="title of the edital.")
     */
    public $title;

    /**
     * @var string|null
     *
     * @Serializer\Groups({"public"})
     *
     * @SWG\Property(type="string", description="Publication date of the edital")
     */
    public $date;

    /**
     * @var string|null
     *
     * @Serializer\Groups({"public"})
     *
     * @SWG\Property(type="string", maxLength=255, description="title of the edital.")
     */
    public $number;

    /**
     * @var string|null
     *
     * @Serializer\Groups({"public"})
     *
     * @SWG\Property(type="boolean", default="false", description="Open subscription.")
     */
    public $subscription;

    /**
     * @var string|null
     *
     * @Serializer\Groups({"public"})
     *
     * @SWG\Property(type="string", description="URL of the edital.")
     */
    public $url;

    /**
     * @var string|null
     *
     * @Serializer\Groups({"public"})
     *
     * @SWG\Property(type="string", description="About of the edital.")
     */
    public $about;

    /**
     * @var string|null
     *
     * @Serializer\Groups({"public"})
     *
     * @SWG\Property(type="string", description="Job description of the edital.")
     */
    public $job;

    /**
     * @var string|null
     *
     * @Serializer\Groups({"public"})
     *
     * @SWG\Property(type="string", description="Value  of the edital.")
     */
    public $value;

    /**
     * @var string|null
     *
     * @Serializer\Groups({"public"})
     *
     * @SWG\Property(type="string", description="Info about main contact")
     */
    public $contact;
}
