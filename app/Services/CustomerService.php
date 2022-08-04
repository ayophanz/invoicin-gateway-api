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

    public function fetchCustomers(Request $request)
    {
        return $this->forwardRequest('/api/customers', $request);
    }

    public function storeCustomer(Request $request)
    {
        return $this->forwardRequest('/api/customers/store', $request);
    }

    public function updateCustomer(Request $request, $id)
    {
        return $this->forwardRequest('/api/customers/update/' . $id, $request);
    }

    public function destroyCustomer(Request $request, $id)
    {
        return $this->forwardRequest('/api/customers/destroy/' . $id, $request);
    }
}
