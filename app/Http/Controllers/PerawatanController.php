<?php

namespace App\Http\Controllers;

use App\Models\Perawatan;
use App\Models\Ternak;
use Illuminate\Http\Request;

class PerawatanController extends Controller
{
    private const FILTER = ['terlambat' => 'Terlambat', 'hari_ini' => 'Hari ini', 'mendatang' => 'Mendatang', 'selesai' => 'Selesai'];

    private function bisaHapus(): bool
    {
        return auth()->user()->role === 'pemilik';
    }

    public function index(Request $request)
    {
        $s = $request->query('s');
        $q = trim((string) $request->query('q', ''));

        $items = Perawatan::query()
            ->with('ternak:id,kode,jenis,ras')
            ->when($s === 'terlambat', fn ($x) => $x->terlambat())
            ->when($s === 'hari_ini', fn ($x) => $x->hariIni())
            ->when($s === 'mendatang', fn ($x) => $x->mendatang())
            ->when($s === 'selesai', fn ($x) => $x->selesai())
            ->when($q !== '', fn ($x) => $x->whereHas('ternak', fn ($t) => $t->where('kode', 'like', "%{$q}%")))
            ->orderByRaw("status = 'Selesai'")   // yang belum selesai di atas
            ->orderBy('jadwal')
            ->paginate(10)
            ->withQueryString();

        $jumlah = [
            'semua'     => Perawatan::count(),
            'terlambat' => Perawatan::terlambat()->count(),
            'hari_ini'  => Perawatan::hariIni()->count(),
            'mendatang' => Perawatan::mendatang()->count(),
            'selesai'   => Perawatan::selesai()->count(),
        ];

        return view('perawatan.index', [
            'items' => $items, 'jumlah' => $jumlah, 'filter' => self::FILTER,
            's' => $s, 'q' => $q, 'bisaHapus' => $this->bisaHapus(),
        ]);
    }

    public function create(Request $request)
    {
        $p = new Perawatan(['jadwal' => now()->addDay()->setTime(9, 0), 'ternak_id' => $request->query('ternak')]);

        return view('perawatan.form', ['perawatan' => $p, 'daftarTernak' => $this->daftarTernak()]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);
        $data['user_id'] = $request->user()->id;
        $data['status']  = 'Dijadwalkan';
        $p = Perawatan::create($data);

        return redirect()->route('perawatan.index')->with('sukses', "Perawatan {$p->jenis} untuk {$p->ternak->kode} dijadwalkan.");
    }

    public function edit(Perawatan $perawatan)
    {
        return view('perawatan.form', ['perawatan' => $perawatan, 'daftarTernak' => $this->daftarTernak($perawatan->ternak_id)]);
    }

    public function update(Request $request, Perawatan $perawatan)
    {
        $perawatan->update($this->validasi($request));

        return redirect()->route('perawatan.index')->with('sukses', 'Jadwal perawatan diperbarui.');
    }

    /** Catat bahwa perawatan sudah dilakukan (tombol "Catat perawatan"). */
    public function selesai(Request $request, Perawatan $perawatan)
    {
        if ($perawatan->status === 'Selesai') {
            return back()->with('galat', 'Perawatan ini sudah dicatat selesai.');
        }

        $data = $request->validate([
            'biaya'   => ['nullable', 'integer', 'min:0'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ], [
            'biaya.integer' => 'Biaya harus berupa bilangan bulat.',
            'biaya.min'     => 'Biaya tidak boleh negatif.',
            'catatan.max'   => 'Catatan terlalu panjang.',
        ]);

        $perawatan->update([
            'status'          => 'Selesai',
            'tanggal_selesai' => now(),
            'biaya'           => $data['biaya'] ?? $perawatan->biaya,
            'catatan'         => $data['catatan'] ?? $perawatan->catatan,
        ]);

        return back()->with('sukses', "Perawatan {$perawatan->jenis} untuk {$perawatan->ternak->kode} dicatat selesai.");
    }

    public function destroy(Perawatan $perawatan)
    {
        $perawatan->delete();

        return redirect()->route('perawatan.index')->with('sukses', 'Jadwal perawatan dihapus.');
    }

    /** Riwayat kesehatan satu ekor ternak (KF-07). */
    public function riwayat(Ternak $ternak)
    {
        $items = Perawatan::with('user:id,name')
            ->where('ternak_id', $ternak->id)
            ->orderByDesc('jadwal')
            ->get();

        return view('perawatan.riwayat', ['ternak' => $ternak, 'items' => $items]);
    }

    private function daftarTernak(?int $termasukId = null)
    {
        return Ternak::query()
            ->where(fn ($q) => $q->where('status', '!=', 'Terjual')->when($termasukId, fn ($y) => $y->orWhere('id', $termasukId)))
            ->orderBy('kode')
            ->get(['id', 'kode', 'jenis', 'ras']);
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'ternak_id' => ['required', 'exists:ternak,id'],
            'jenis'     => ['required', 'in:'.implode(',', Perawatan::JENIS)],
            'jadwal'    => ['required', 'date'],
            'biaya'     => ['nullable', 'integer', 'min:0'],
            'catatan'   => ['nullable', 'string', 'max:1000'],
        ], [
            'required' => ':attribute wajib diisi.',
            'exists'   => ':attribute tidak ditemukan.',
            'in'       => ':attribute tidak valid.',
            'date'     => ':attribute bukan tanggal dan jam yang valid.',
            'integer'  => ':attribute harus berupa bilangan bulat.',
            'min'      => ':attribute tidak boleh negatif.',
            'max'      => ':attribute terlalu panjang.',
        ], [
            'ternak_id' => 'Ternak', 'jenis' => 'Jenis perawatan', 'jadwal' => 'Jadwal',
            'biaya' => 'Biaya', 'catatan' => 'Catatan',
        ]);
    }
}
