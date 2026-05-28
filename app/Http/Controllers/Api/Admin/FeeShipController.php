<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveFeeShipRequest;
use App\Models\Province;
use App\Models\Transport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class FeeShipController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Transport::query()
                ->with('province:id,name,type')
                ->latest()
                ->get()
                ->map(fn (Transport $feeShip) => $this->formatFeeShip($feeShip)),
        ]);
    }

    public function store(SaveFeeShipRequest $request): JsonResponse
    {
        $data = $request->validated();

        $feeShip = DB::transaction(function () use ($data) {
            $province = Province::create([
                'name' => $data['province_name'],
                'type' => $data['province_type'],
            ]);

            return Transport::create([
                'province_id' => $province->id,
                'price' => $data['price'],
            ]);
        });

        return response()->json([
            'message' => __('translate.createSuccess'),
            'data' => $this->formatFeeShip($feeShip->load('province:id,name,type')),
        ], 201);
    }

    public function show(Transport $feeship): JsonResponse
    {
        return response()->json([
            'data' => $this->formatFeeShip($feeship->load('province:id,name,type')),
        ]);
    }

    public function update(SaveFeeShipRequest $request, Transport $feeship): JsonResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $feeship) {
            $feeship->province()->update([
                'name' => $data['province_name'],
                'type' => $data['province_type'],
            ]);

            $feeship->update([
                'price' => $data['price'],
            ]);
        });

        return response()->json([
            'message' => __('translate.updateSuccess'),
            'data' => $this->formatFeeShip($feeship->refresh()->load('province:id,name,type')),
        ]);
    }

    public function destroy(Transport $feeship): JsonResponse
    {
        DB::transaction(function () use ($feeship) {
            $province = $feeship->province;
            $feeship->delete();
            $province?->delete();
        });

        return response()->json(null, 204);
    }

    private function formatFeeShip(Transport $feeShip): array
    {
        return [
            'id' => $feeShip->id,
            'province_id' => $feeShip->province_id,
            'province_name' => $feeShip->province?->name,
            'province_type' => $feeShip->province?->type,
            'price' => $feeShip->price,
            'created_at' => optional($feeShip->created_at)->toDateTimeString(),
            'updated_at' => optional($feeShip->updated_at)->toDateTimeString(),
        ];
    }
}
