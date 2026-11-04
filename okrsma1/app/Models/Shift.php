<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = ['name', 'start_time', 'end_time', 'color', 'active', 'note'];
    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }
    public function assignments()
    {
        return $this->hasMany(ShiftAssignment::class);
    }
}
