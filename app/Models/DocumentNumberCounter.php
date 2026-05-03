<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentNumberCounter extends Model
{
    protected $fillable = [
        'document_type_id', 'enabled', 'format', 'current_number',
        'number_padding', 'reset_on', 'last_reset_year', 
        'last_reset_month', 'field_key',
    ];

    protected function casts(): array {
        return ['enabled' => 'boolean'];
    }

    public function documentType(): BelongsTo {
        return $this->belongsTo(DocumentType::class);
    }

    public function generateNext(): string {
        return \DB::transaction(function() {
            // re fetch while loch to avoid race condition
            $counter = self::lockForUpdate()->find($this->id);

            $now = Carbon::now();
            $year = (int) $now->format('Y');
            $month = (int) $now->format('m');

            // auto reset
            $needsReset = match($counter->reset_on) {
                'yearly' => $counter->last_reset_year !== $year,
                'monthly' => $counter->last_reset_year !== $year || $counter->last_reset_month !== $month,
                default => false,
            };

            if ($needsReset) {
                $counter->current_number = 0;
                $counter->last_reset_year = $year;
                $counter->last_reset_month = $month;
            }

            $counter->current_number += 1;
            $counter->save();

            return $counter->format($counter->current_number);
        });
    }

    public function format(int $number): string {
        $now = Carbon::now();
        $padded = str_pad($number, $this->number_padding, '0', STR_PAD_LEFT);

        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];

        return str_replace(
            ['{number}', '{year}', '{month}', '{roman_month}'],
            [
                $padded,
                $now->format('Y'),
                $now->format('m'),
                $romanMonths[(int) $now->format('m')],
            ],
            $this->format
        );
    }

    public function previewNext(): string {
        return $this->format($this->current_number + 1);
    }
}
