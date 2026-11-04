<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Ternak extends Model
{
    protected $table = 'ternak';

    public const JENIS   = ['Kambing', 'Sapi', 'Domba'];
    public const KELAMIN = ['Jantan', 'Betina'];
    public const STATUS  = ['Tersedia', 'Dirawat', 'Terjual'];

    protected $fillable = [
        'kode', 'jenis', 'ras', 'jenis_kelamin', 'tanggal_lahir',
        'bobot_kg', 'status', 'harga_beli', 'catatan',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'bobot_kg'      => 'float',
    ];

    /** Ternak yang sudah tercatat di sebuah pesanan tidak boleh dihapus. */
    public function punyaPesanan(): bool
    {
        return DB::table('detail_pesanan')->where('ternak_id', $this->id)->exists();
    }

    /** Umur dalam teks, dihitung dari tanggal lahir. */
    public function getUmurAttribute(): string
    {
        if (! $this->tanggal_lahir) {
            return '-';
        }
        $bulan = (int) $this->tanggal_lahir->diffInMonths(now());
        if ($bulan < 12) {
            return $bulan.' bln';
        }

        return intdiv($bulan, 12).' th'.($bulan % 12 ? ' '.($bulan % 12).' bln' : '');
    }
}
