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

    /** Organization */
    public function fetchOrganization(Request $request)
    {
        return $this->forwardRequest('/api/organization/show', $request);
    }

    public function storeOrganization(Request $request)
    {
        return $this->forwardRequest('/api/organization/store', $request);
    }

    /** Organization settings */
    public function storeSetting(Request $request)
    {
        return $this->forwardRequest('/api/organization/settings/store', $request);
    }

    /** Organization addresses */
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
