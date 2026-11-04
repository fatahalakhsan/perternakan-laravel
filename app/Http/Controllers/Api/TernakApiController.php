<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TernakRequest;
use App\Http\Resources\TernakResource;
use App\Models\Ternak;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class TernakApiController extends Controller
{
    /** GET /api/ternak?status=&q=&page=&per_page=   -> 200 */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $q      = trim((string) $request->query('q', ''));
        $per    = min(max((int) $request->query('per_page', 10), 1), 50);

        $items = Ternak::query()
            ->when(in_array($status, Ternak::STATUS, true), fn ($x) => $x->where('status', $status))
            ->when($q !== '', fn ($x) => $x->where(fn ($y) => $y->where('kode', 'like', "%{$q}%")->orWhere('ras', 'like', "%{$q}%")))
            ->orderBy('kode')
            ->paginate($per)
            ->withQueryString();

        return TernakResource::collection($items);
    }

    /** GET /api/ternak/{id}   -> 200, 404 jika id tidak ada */
    public function show(Ternak $ternak): TernakResource
    {
        return new TernakResource($ternak);
    }

    /** POST /api/ternak   -> 201, 400 jika input salah */
    public function store(TernakRequest $request): JsonResponse
    {
        $ternak = Ternak::create($request->validated());

        return (new TernakResource($ternak))->response()->setStatusCode(201);
    }

    /** PUT /api/ternak/{id}   -> 200, 400, 404 */
    public function update(TernakRequest $request, Ternak $ternak): TernakResource
    {
        $data = $request->validated();

        // Aturan bisnis: ternak yang sudah ada di pesanan harus tetap berstatus Terjual
        if ($ternak->punyaPesanan() && $data['status'] !== 'Terjual') {
            throw ValidationException::withMessages(['status' => 'Ternak ini sudah ada di pesanan, statusnya harus Terjual.']);
        }

        $ternak->update($data);

        return new TernakResource($ternak->fresh());
    }

    /** DELETE /api/ternak/{id}   -> 204, 404, 409 jika sudah ada di pesanan */
    public function destroy(Ternak $ternak): JsonResponse|Response
    {
        if ($ternak->punyaPesanan()) {
            return response()->json(['message' => "Ternak {$ternak->kode} tertaut pesanan dan tidak dapat dihapus."], 409);
        }

        $ternak->delete();

        return response()->noContent();
    }
}
