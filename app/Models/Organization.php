<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Organization extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function __construct(array $attributes = [], $data = [])
    {
        parent::__construct($attributes);

        foreach ($data as $k => $v) {
            $this->$k = $v;
        }
    }
}
