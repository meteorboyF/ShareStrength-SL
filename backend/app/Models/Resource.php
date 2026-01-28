<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'uploaded_by',
        'title',
        'description',
        'file_path',
        'file_type',
        'file_size',
        'is_public',
        'type',
        'category_id',
        'file_url',
        'duration',
        'language',
        'author',
        'narrator',
        'task_id',
        'is_featured',
        'download_count',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'download_count' => 'integer',
        'file_size' => 'integer',
        'duration' => 'integer',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(ResourceCategory::class, 'category_id');
    }

    public function uploader()
    {
        return $this->belongsTo(Admin::class, 'uploaded_by');
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // Helper methods
    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size)
            return 'Unknown';

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getFormattedDurationAttribute()
    {
        if (!$this->duration)
            return null;

        $hours = floor($this->duration / 3600);
        $minutes = floor(($this->duration % 3600) / 60);
        $seconds = $this->duration % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%d:%02d', $minutes, $seconds);
    }
}
