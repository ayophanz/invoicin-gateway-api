<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\OrganizationService;
use App\Traits\UUID;
use App\Traits\ApiResponser;

class Organization extends Model
{
    use HasFactory, SoftDeletes, UUID, ApiResponser;

    private $organizationService;
    private $request;

    public function __construct()
    {
        $this->organizationService = new OrganizationService();
        $this->request             = request();
    }

    protected $casts = [
        'settings' => 'array',
        'addresses' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'organization_id',
        'name',
        'email',
        'type',
    ];

    public function getSettingsAttribute()
    {
        $settings = $this->organizationService->fetchSettings($this->request);
        $decoded = json_decode($settings->original, true);
        return $decoded;
    }

    public function getAddressesAttribute()
    {
        $addresses = $this->organizationService->fetchAddresses($this->request);
        $decoded = json_decode($addresses->original, true);
        return $decoded;
    }
}
