<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Perawatan extends Model
{
    protected $table = 'perawatan';

    public const JENIS = ['Vaksin', 'Pakan', 'Obat', 'Penimbangan', 'Pemeriksaan'];

    protected $fillable = [
        'ternak_id', 'user_id', 'jenis', 'jadwal', 'status',
        'tanggal_selesai', 'biaya', 'catatan',
    ];

    protected $casts = [
        'jadwal'          => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    public function ternak(): BelongsTo
    {
        return $this->belongsTo(Ternak::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ---- Filter status berdasarkan tanggal (cocok dengan label di tampilan) ----
    public function scopeTerlambat(Builder $q): Builder
    {
        return $q->where('status', 'Dijadwalkan')->where('jadwal', '<', now()->startOfDay());
    }

    public function scopeHariIni(Builder $q): Builder
    {
        return $q->where('status', 'Dijadwalkan')->whereBetween('jadwal', [now()->startOfDay(), now()->endOfDay()]);
    }

    public function scopeMendatang(Builder $q): Builder
    {
        return $q->where('status', 'Dijadwalkan')->where('jadwal', '>', now()->endOfDay());
    }

    public function scopeSelesai(Builder $q): Builder
    {
        return $q->where('status', 'Selesai');
    }

    /** Label status untuk tampilan: Selesai, Terlambat, Hari ini, atau Dijadwalkan. */
    public function getStatusTampilAttribute(): string
    {
        if ($this->status === 'Selesai') {
            return 'Selesai';
        }
        if ($this->jadwal->isToday()) {
            return 'Hari ini';
        }

        return $this->jadwal->isPast() ? 'Terlambat' : 'Dijadwalkan';
    }

    public function getKelasBadgeAttribute(): string
    {
        return ['Selesai' => 'ok', 'Terlambat' => 'd', 'Hari ini' => 'w'][$this->status_tampil] ?? '';
    }

    public static function tglIndo(?Carbon $c): string
    {
        if (! $c) {
            return '-';
        }
        $bln = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        return $c->day.' '.$bln[$c->month - 1].' '.$c->year.', '.$c->format('H.i');
    }
}
