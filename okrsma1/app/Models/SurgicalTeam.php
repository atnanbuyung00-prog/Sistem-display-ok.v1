<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurgicalTeam extends Model
{
    protected $fillable = ['name', 'role', 'profession_id', 'active'];
    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }
    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }
    public function schedules()
    {
        return $this->belongsToMany(SurgerySchedule::class, 'surgery_schedule_surgical_team');
    }
}
