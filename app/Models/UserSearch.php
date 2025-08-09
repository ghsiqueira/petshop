<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'query',
        'type',
        'filters',
        'results_count',
        'ip_address'
    ];

    protected $casts = [
        'filters' => 'array'
    ];

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Métodos estáticos
    public static function getPopularSearches($type = null, $limit = 10)
    {
        $query = static::selectRaw('query, COUNT(*) as search_count')
            ->where('created_at', '>=', now()->subDays(30))
            ->where('query', '!=', '')
            ->groupBy('query')
            ->orderBy('search_count', 'desc')
            ->limit($limit);

        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }

        return $query->get();
    }

    public static function getUserRecentSearches($userId, $limit = 5)
    {
        return static::where('user_id', $userId)
            ->where('query', '!=', '')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->unique('query')
            ->values();
    }

    public static function recordSearch($query, $type = 'general', $filters = [], $resultsCount = 0)
    {
        if (empty(trim($query))) {
            return null;
        }

        return static::create([
            'user_id' => auth()->id(),
            'query' => trim($query),
            'type' => $type,
            'filters' => $filters,
            'results_count' => $resultsCount,
            'ip_address' => request()->ip()
        ]);
    }

    // Scopes
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeWithResults($query)
    {
        return $query->where('results_count', '>', 0);
    }
}
