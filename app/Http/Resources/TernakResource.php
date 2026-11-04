<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Bentuk JSON satu ternak (kontrak respons API). */
class TernakResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'kode'          => $this->kode,
            'jenis'         => $this->jenis,
            'ras'           => $this->ras,
            'jenis_kelamin' => $this->jenis_kelamin,
            'tanggal_lahir' => $this->tanggal_lahir?->toDateString(),
            'umur'          => $this->umur,
            'bobot_kg'      => $this->bobot_kg,
            'status'        => $this->status,
            'harga_beli'    => $this->harga_beli,
            'catatan'       => $this->catatan,
            'dibuat'        => $this->created_at?->toDateTimeString(),
        ];
    }
}
