<?php

namespace App\Services;

use App\Traits\RequestService;
use Illuminate\Http\Request;

/**
 * Class OrganizationService.
 */
class OrganizationService
{
    use RequestService;

    public $baseUri;

    public function __construct()
    {
        $this->baseUri = config('services.organization.base_uri');
    }

    public function fetchSetting()
    {
        return $this->request('GET', '/api/setting');
    }
}
