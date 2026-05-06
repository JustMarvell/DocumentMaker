<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PdfConversionSetting extends Model
{
    protected $fillable = [
        'monthly_limit', 'used_count', 'reset_on',
        'last_reset_year', 'last_reset_month',
        'iloveapi_public_key', 'iloveapi_secret_key',
    ];
 
    /** Get the singleton row, creating it if absent */
    public static function instance(): self
    {
        return self::firstOrCreate([], [
            'monthly_limit' => 250,
            'used_count'    => 0,
            'reset_on'      => 'monthly',
        ]);
    }
 
    public function hasQuota(): bool
    {
        $this->autoReset();
        return $this->used_count < $this->monthly_limit;
    }
 
    public function remaining(): int
    {
        $this->autoReset();
        return max(0, $this->monthly_limit - $this->used_count);
    }
 
    public function increment(): void
    {
        $this->autoReset();
        $this->increment('used_count');
    }
 
    public function resetNow(): void
    {
        $now = Carbon::now();
        $this->update([
            'used_count'       => 0,
            'last_reset_year'  => (int) $now->format('Y'),
            'last_reset_month' => (int) $now->format('m'),
        ]);
    }
 
    private function autoReset(): void
    {
        if ($this->reset_on !== 'monthly') return;
 
        $now   = Carbon::now();
        $year  = (int) $now->format('Y');
        $month = (int) $now->format('m');
 
        if ($this->last_reset_year !== $year || $this->last_reset_month !== $month) {
            $this->update([
                'used_count'       => 0,
                'last_reset_year'  => $year,
                'last_reset_month' => $month,
            ]);
            // refresh local attrs
            $this->used_count = 0;
        }
    }

}
