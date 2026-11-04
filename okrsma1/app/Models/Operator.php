<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operator extends Model
{
    protected $fillable = ['name', 'license_no', 'specialty', 'active'];
    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }
    public function schedules()
    {
        return $this->hasMany(SurgerySchedule::class);
    }
}
