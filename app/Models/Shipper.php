<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipper extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['contacts', 'primaryContact'];

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function primaryContact(): HasMany
    {
        return $this->contacts()->where('is_primary', 1);
    }
}
