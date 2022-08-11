<?php

namespace App\Services;

use App\Traits\RequestService;
use Illuminate\Http\Request;

/**
 * Class CustomerService.
 */
class CustomerService
{
    use RequestService;

    public $baseUri;

    public function __construct()
    {
        $this->baseUri = config('services.customer.base_uri');
    }

    /** Customer */
    public function fetchCustomers(Request $request)
    {
        return $this->forwardRequest('/api/customers', $request);
    }

    public function fetchCustomer(Request $request, $id)
    {
        return $this->forwardRequest('/api/customers/' . $id .'/show', $request);
    }

    public function storeCustomer(Request $request)
    {
        return $this->forwardRequest('/api/customers/store', $request);
    }

    public function updateCustomer(Request $request, $id)
    {
        return $this->forwardRequest('/api/customers/' . $id . '/update', $request);
    }

    public function destroyCustomer(Request $request, $id)
    {
        return $this->forwardRequest('/api/customers/' . $id . '/destroy', $request);
    }

    /** Customer Address */
    public function fetchAddress(Request $request, $id)
    {
        return $this->forwardRequest('/api/customers/' . $id . '/addresses/show', $request);
    }

    public function storeAddress(Request $request, $id)
    {
        return $this->forwardRequest('/api/customers/' . $id . '/addresses/store', $request);
    }

    public function updateAddress(Request $request, $id)
    {
        return $this->forwardRequest('/api/customers/' . $id . '/addresses/update', $request);
    }

    public function destroyAddress(Request $request, $id)
    {
        return $this->forwardRequest('/api/customers/' . $id . '/addresses/destroy', $request);
    }

    /** Customer Setting */
    public function showSetting(Request $request, $id)
    {
        return $this->forwardRequest('/api/customers/' . $id . '/settings/show', $request);
    }

    public function storeSetting(Request $request, $id)
    {
        return $this->forwardRequest('/api/customers/' . $id . '/settings/store', $request);
    }

    public function updateSetting(Request $request, $id)
    {
        return $this->forwardRequest('/api/customers/' . $id . '/settings/update', $request);
    }

    public function destroySetting(Request $request, $id)
    {
        return $this->forwardRequest('/api/customers/' . $id . '/settings/destroy', $request);
    }
}
