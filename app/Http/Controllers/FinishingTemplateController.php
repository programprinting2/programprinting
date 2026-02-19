<?php

namespace App\Http\Controllers;

use App\Models\MasterParameter;
use App\Models\DetailParameter;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FinishingTemplateController extends Controller
{
    public function index(): JsonResponse
    {
        $parameter = MasterParameter::query()
            ->where('nama_parameter', 'FINISHING TEMPLATE')
            ->first();

        if (!$parameter) {
            return response()->json([
                'success' => true,
                'templates' => [],
            ]);
        }

        $templates = DetailParameter::query()
            ->where('master_parameter_id', $parameter->id)
            ->orderBy('nama_detail_parameter')
            ->get(['id', 'nama_detail_parameter']);

        return response()->json([
            'success'   => true,
            'templates' => $templates->map(fn ($t) => [
                'id'   => $t->id,
                'nama' => $t->nama_detail_parameter,
            ]),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $detail = DetailParameter::query()
            ->whereHas('masterParameter', fn ($q) => $q->where('nama_parameter', 'FINISHING TEMPLATE'))
            ->findOrFail($id);

        $payload = json_decode($detail->keterangan, true);
        if (!is_array($payload)) {
            $payload = [];
        }

        return response()->json([
            'success' => true,
            'nama'    => $detail->nama_detail_parameter,
            'payload' => $payload,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama'    => ['required', 'string', 'max:255'],
            'payload' => ['required', 'array'],
        ]);

        // Cari parameter utama FINISHING TEMPLATE
        $parameter = MasterParameter::query()
            ->where('nama_parameter', 'FINISHING TEMPLATE')
            ->firstOrFail();

        // /** @var \App\Models\DetailParameter $detail */
        $templatePayload = $data['payload'];
        unset($templatePayload['AlamatFile']);
        
        $detail = new DetailParameter();
        $detail->master_parameter_id = $parameter->id;
        $detail->nama_detail_parameter = $data['nama']; // sesuaikan dengan nama kolom
        $detail->keterangan = json_encode($data['payload'], JSON_UNESCAPED_UNICODE); // atau kolom lain untuk json
        $detail->save();

        return response()->json([
            'success' => true,
            'message' => 'Template berhasil disimpan.',
            'id'      => $detail->id,
        ]);
    }
}