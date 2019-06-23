<?php

declare(strict_types=1);

namespace App\Application\Http\Repository;

use App\Application\Http\Repository\Exception\ClientException;
use App\Application\Http\Repository\Request\Repository;
use App\Application\Http\Repository\Response\Information;

interface Client
{
    /**
     * @throws ClientException
     */
    public function getInformation(Repository $repository): Information;
}
