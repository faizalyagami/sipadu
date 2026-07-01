<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportProgress extends Model
{
    protected $table = 'import_progress';
    
    protected $fillable = [
        'batch_id',
        'filename',
        'total_rows',
        'processed_rows',
        'success_rows',
        'failed_rows',
        'status',
        'errors'
    ];
    
    protected $casts = [
        'errors' => 'array',
        'total_rows' => 'integer',
        'processed_rows' => 'integer',
        'success_rows' => 'integer',
        'failed_rows' => 'integer'
    ];
    
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    
    /**
     * Update progress
     */
    public function updateProgress($processed, $success, $failed)
    {
        $this->processed_rows = $processed;
        $this->success_rows = $success;
        $this->failed_rows = $failed;
        $this->save();
    }
    
    /**
     * Get percentage progress
     */
    public function getPercentageAttribute()
    {
        if ($this->total_rows <= 0) {
            return 0;
        }
        
        return round(($this->processed_rows / $this->total_rows) * 100);
    }
    
    /**
     * Check if import is completed
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }
    
    /**
     * Check if import is failed
     */
    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }
    
    /**
     * Check if import is processing
     */
    public function isProcessing()
    {
        return $this->status === self::STATUS_PROCESSING;
    }
}