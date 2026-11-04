<?php

namespace App\Http\Controllers;

use App\Models\Ternak;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class TernakController extends Controller
{
    private function bisaUbah(): bool
    {
        return in_array(auth()->user()->role, ['pemilik', 'pekerja'], true);
    }

    public function index(Request $request)
    {
        $status = $request->query('status');
        $q      = trim((string) $request->query('q', ''));

        $items = Ternak::query()
            ->when(in_array($status, Ternak::STATUS, true), fn ($x) => $x->where('status', $status))
            ->when($q !== '', fn ($x) => $x->where(fn ($y) => $y->where('kode', 'like', "%{$q}%")->orWhere('ras', 'like', "%{$q}%")))
            ->orderBy('kode')
            ->paginate(10)
            ->withQueryString();

        $jumlah = Ternak::selectRaw('status, COUNT(*) AS n')->groupBy('status')->pluck('n', 'status');

        return view('ternak.index', [
            'items'    => $items,
            'jumlah'   => $jumlah,
            'status'   => $status,
            'q'        => $q,
            'bisaUbah' => $this->bisaUbah(),
        ]);
    }

    public function create()
    {
        return view('ternak.form', ['ternak' => new Ternak(['jenis_kelamin' => 'Jantan', 'status' => 'Tersedia'])]);
    }

    public function store(Request $request)
    {
        $request->merge(['kode' => strtoupper(trim((string) $request->input('kode')))]);
        $data   = $this->validasi($request);
        $ternak = Ternak::create($data);

        return redirect()->route('ternak.index')->with('sukses', "Ternak {$ternak->kode} ditambahkan.");
    }

    public function edit(Ternak $ternak)
    {
        return view('ternak.form', ['ternak' => $ternak]);
    }

    public function update(Request $request, Ternak $ternak)
    {
        $data = $this->validasi($request, $ternak);

        // Ternak yang sudah ada di pesanan harus tetap berstatus Terjual
        if ($ternak->punyaPesanan() && $data['status'] !== 'Terjual') {
            return back()->withInput()->withErrors(['status' => 'Ternak ini sudah ada di pesanan, statusnya harus Terjual.']);
        }

        $ternak->update($data);

        return redirect()->route('ternak.index')->with('sukses', "Ternak {$ternak->kode} diperbarui.");
    }

    public function destroy(Ternak $ternak)
    {
        if ($ternak->punyaPesanan()) {
            return back()->with('galat', "{$ternak->kode} tertaut pesanan dan tidak dapat dihapus.");
        }

        try {
            $ternak->delete();
        } catch (QueryException $e) {
            return back()->with('galat', "{$ternak->kode} tidak dapat dihapus karena masih dipakai data lain.");
        }

        return redirect()->route('ternak.index')->with('sukses', "Ternak {$ternak->kode} dihapus.");
    }

    private function validasi(Request $request, ?Ternak $ternak = null): array
    {
        $rules = [
            'jenis'         => ['required', 'in:'.implode(',', Ternak::JENIS)],
            'ras'           => ['nullable', 'string', 'max:60'],
            'jenis_kelamin' => ['required', 'in:'.implode(',', Ternak::KELAMIN)],
            'tanggal_lahir' => ['nullable', 'date', 'before_or_equal:today'],
            'bobot_kg'      => ['required', 'numeric', 'gt:0', 'max:9999.9'],
            'status'        => ['required', 'in:'.implode(',', Ternak::STATUS)],
            'harga_beli'    => ['nullable', 'integer', 'min:0'],
            'catatan'       => ['nullable', 'string', 'max:1000'],
        ];
        if (! $ternak) {
            $rules['kode'] = ['required', 'regex:/^[A-Z]{2}-\d{3}$/', 'unique:ternak,kode'];
        }

        return $request->validate($rules, [
            'required'        => ':attribute wajib diisi.',
            'in'              => ':attribute tidak valid.',
            'unique'          => ':attribute sudah dipakai ternak lain.',
            'numeric'         => ':attribute harus berupa angka.',
            'integer'         => ':attribute harus berupa bilangan bulat.',
            'gt'              => ':attribute harus lebih dari 0.',
            'min'             => ':attribute tidak boleh negatif.',
            'max'             => ':attribute terlalu besar atau panjang.',
            'date'            => ':attribute bukan tanggal yang valid.',
            'before_or_equal' => 'Tanggal lahir tidak boleh di masa depan.',
            'kode.regex'      => 'Format kode: dua huruf, strip, tiga angka (contoh KB-030).',
        ], [
            'kode' => 'Kode', 'jenis' => 'Jenis', 'ras' => 'Ras', 'jenis_kelamin' => 'Jenis kelamin',
            'tanggal_lahir' => 'Tanggal lahir', 'bobot_kg' => 'Bobot', 'status' => 'Status',
            'harga_beli' => 'Harga beli', 'catatan' => 'Catatan',
        ]);
    }
}
