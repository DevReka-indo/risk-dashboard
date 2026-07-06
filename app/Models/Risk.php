<?php

namespace App\Models;

use Database\Factories\RiskFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Risk extends Model
{
    /** @use HasFactory<RiskFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'department',
        'category',
        'level',
        'nilai',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'nilai' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getLevelColorClass(): string
    {
        return match ($this->level) {
            'Low' => 'bg-emerald-100 text-emerald-700',
            'Low to Moderate' => 'bg-yellow-100 text-yellow-700',
            'High' => 'bg-red-100 text-red-600',
            'Critical' => 'bg-rose-100 text-rose-700',
            default => 'bg-slate-100 text-slate-600',
        };
    }

    public function getStatusColorClass(): string
    {
        return match ($this->status) {
            'Open' => 'bg-yellow-100 text-yellow-700',
            'In Progress' => 'bg-blue-100 text-blue-700',
            'Closed' => 'bg-emerald-100 text-emerald-700',
            default => 'bg-slate-100 text-slate-600',
        };
    }
}
