<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class UserPhotoHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'photo_path',
        'photo_type',
        'related_id',
        'uploaded_at',
        'deleted_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'uploaded_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user that owns the photo history.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related model (Pet or Petshop) based on photo_type and related_id
     */
    public function getRelatedModelAttribute()
    {
        if (!$this->related_id) {
            return null;
        }

        switch ($this->photo_type) {
            case 'pet':
                return Pet::find($this->related_id);
            case 'logo':
                return Petshop::find($this->related_id);
            default:
                return null;
        }
    }

    /**
     * Get the photo URL
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo_path) {
            return null;
        }

        return asset('storage/' . $this->photo_path);
    }

    /**
     * Scope to filter by photo type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('photo_type', $type);
    }

    /**
     * Scope to filter by user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get recent photos
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('uploaded_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Scope to get deleted photos
     */
    public function scopeDeleted($query)
    {
        return $query->whereNotNull('deleted_at');
    }

    /**
     * Scope to get active photos
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Mark photo as deleted
     */
    public function markAsDeleted(): bool
    {
        return $this->update(['deleted_at' => now()]);
    }

    /**
     * Restore deleted photo
     */
    public function restore(): bool
    {
        return $this->update(['deleted_at' => null]);
    }

    /**
     * Get formatted uploaded date
     */
    public function getFormattedUploadedAtAttribute(): string
    {
        return $this->uploaded_at ? $this->uploaded_at->format('d/m/Y H:i') : '';
    }

    /**
     * Get formatted deleted date
     */
    public function getFormattedDeletedAtAttribute(): string
    {
        return $this->deleted_at ? $this->deleted_at->format('d/m/Y H:i') : '';
    }

    /**
     * Get time since uploaded
     */
    public function getTimeSinceUploadedAttribute(): string
    {
        return $this->uploaded_at ? $this->uploaded_at->diffForHumans() : '';
    }

    /**
     * Check if photo is recent (uploaded in last 24 hours)
     */
    public function getIsRecentAttribute(): bool
    {
        return $this->uploaded_at && $this->uploaded_at->isToday();
    }

    /**
     * Get photo type in Portuguese
     */
    public function getPhotoTypeInPortugueseAttribute(): string
    {
        $types = [
            'profile' => 'Foto de Perfil',
            'pet' => 'Foto de Pet',
            'logo' => 'Logo da Empresa'
        ];

        return $types[$this->photo_type] ?? $this->photo_type;
    }

    /**
     * Get file size if exists
     */
    public function getFileSizeAttribute(): ?int
    {
        if (!$this->photo_path) {
            return null;
        }

        $fullPath = storage_path('app/public/' . $this->photo_path);
        
        return file_exists($fullPath) ? filesize($fullPath) : null;
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $size = $this->file_size;
        
        if (!$size) {
            return 'Tamanho desconhecido';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor((strlen($size) - 1) / 3);
        
        return sprintf("%.2f %s", $size / pow(1024, $factor), $units[$factor]);
    }

    /**
     * Static method to clean old backup photos
     */
    public static function cleanOldBackups(int $daysOld = 90): int
    {
        $oldPhotos = static::where('uploaded_at', '<', Carbon::now()->subDays($daysOld))->get();
        $deletedCount = 0;

        foreach ($oldPhotos as $photo) {
            // Delete physical file
            if ($photo->photo_path && \Storage::disk('public')->exists($photo->photo_path)) {
                \Storage::disk('public')->delete($photo->photo_path);
            }
            
            // Delete record
            $photo->delete();
            $deletedCount++;
        }

        return $deletedCount;
    }

    /**
     * Static method to get storage usage by user
     */
    public static function getStorageUsageByUser(int $userId): array
    {
        $photos = static::forUser($userId)->active()->get();
        
        $totalSize = 0;
        $typeUsage = [];

        foreach ($photos as $photo) {
            $size = $photo->file_size ?? 0;
            $totalSize += $size;
            
            if (!isset($typeUsage[$photo->photo_type])) {
                $typeUsage[$photo->photo_type] = ['count' => 0, 'size' => 0];
            }
            
            $typeUsage[$photo->photo_type]['count']++;
            $typeUsage[$photo->photo_type]['size'] += $size;
        }

        return [
            'total_size' => $totalSize,
            'total_count' => $photos->count(),
            'by_type' => $typeUsage,
            'formatted_total_size' => static::formatBytes($totalSize)
        ];
    }

    /**
     * Format bytes to human readable
     */
    private static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}