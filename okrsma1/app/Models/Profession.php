<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    protected $fillable = ['name', 'active'];
    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }
    public function surgicalTeams()
    {
        return $this->hasMany(SurgicalTeam::class);
    }
}
