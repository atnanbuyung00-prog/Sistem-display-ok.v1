<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Greeting extends Model
{
    protected $fillable = ['title', 'message', 'start_time', 'end_time', 'enabled'];
    protected function casts(): array
    {
        return ['enabled' => 'boolean'];
    }
    public function scopeCurrentlyActive($q)
    {
        $time = now('Asia/Jakarta')->format('H:i:s');
        return $q->where('enabled', true)->where(function ($q) use ($time) {
            $q->where(function ($q) use ($time) {
                $q->where('start_time', '<=', $time)->where('end_time', '>=', $time);
            });
        });
    }
}
