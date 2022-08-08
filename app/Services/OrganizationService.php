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

    public function fetchSettings(Request $request)
    {
        return $this->forwardRequest('/api/organization/settings', $request);
    }

    public function fetchAddresses(Request $request)
    {
        return $this->forwardRequest('/api/organization/addresses', $request);
    }

    public function storeAddress(Request $request)
    {
        return $this->forwardRequest('/api/organization/addresses/store', $request);
    }

    public function updateAddress(Request $request)
    {
        return $this->forwardRequest('/api/organization/addresses/update', $request);
    }

    public function destroyAddress(Request $request)
    {
        return $this->forwardRequest('/api/organization/addresses/destroy', $request);
    }
}
