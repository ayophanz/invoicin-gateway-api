<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\OrganizationService;
use App\Traits\UUID;

class Organization extends Model
{
    use HasFactory, SoftDeletes, UUID;

    protected $casts = [
        'setting' => 'array',
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

    public function getSettingAttribute()
    {
        $organizationService  = new OrganizationService();
        $setting = $organizationService->fetchSetting();
        $decode = json_decode($setting->original, true);
        if ($decode['code'] ?? null && $decode['code'] == 401) {
            return $decode['error'];
        }
        return $decode['data'];
    }
}
