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
    public function fetchCustomerAddress(Request $request, $id)
    {
        return $this->forwardRequest('/api/customers/' . $id . '/addresses/show', $request);
    }

    public function storeCustomerAddress(Request $request, $id)
    {
        return $this->forwardRequest('/api/customers/' . $id . '/addresses/store', $request);
    }

    public function updateCustomerAddress(Request $request, $id)
    {
        return $this->forwardRequest('/api/customers/' . $id . '/addresses/update', $request);
    }

    public function destroyCustomerAddress(Request $request, $id)
    {
        return $this->forwardRequest('/api/customers/' . $id . '/addresses/destroy', $request);
    }

    /** Customer Setting */
    public function storeCustomerSetting(Request $request, $id)
    {
        return $this->forwardRequest('/api/customers/' . $id . '/settings/store', $request);
    }

    public function updateCustomerSetting(Request $request, $id)
    {
        return $this->forwardRequest('/api/customers/' . $id . '/settings/update', $request);
    }

    public function destroyCustomerSetting(Request $request, $id)
    {
        return $this->forwardRequest('/api/customers/' . $id . '/settings/destroy', $request);
    }
}
