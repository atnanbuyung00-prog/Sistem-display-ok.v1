<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftAssignment extends Model
{
    protected $fillable = ['shift_id', 'staff_name', 'staff_role', 'shift_date'];
    protected function casts(): array
    {
        return ['shift_date' => 'date'];
    }
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
