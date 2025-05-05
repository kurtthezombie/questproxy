<?php

namespace App\Http\Controllers;

use App\Services\PreferenceService;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PreferenceController extends Controller
{
    use ApiResponseTrait;

    protected $preferenceService;

    public function __construct(PreferenceService $preferenceService)
    {
        $this->preferenceService = $preferenceService;
    }

    public function create(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'game_id' => 'required|integer',
            'service' => ['required', 'string', Rule::in(['level', 'grind', 'farm', 'quest'])],
            'points' => 'nullable|int',
            'duration' => 'nullable|int',
            'max_price' => 'nullable|numeric',
        ]);

        try{
            $preference = $this->preferenceService->create($request->all());

            return $this->successResponse('User preference created.',200, ['data' => $preference]);
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(), 500);
        }
    }

    public function getUserPreference($userId)
    {
        try {
            $preference = $this->preferenceService->getUserPreference($userId);
            
            return $this->successResponse('Preference retrieved.',200,['preference' => $preference]);
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(), 500);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'game_id' => 'sometimes|integer',
            'service' => ['sometimes', 'string', Rule::in(['level', 'grind', 'farm', 'quest'])],
            'points' => 'nullable|int',
            'duration' => 'nullable|int',
            'max_price' => 'nullable|numeric',
        ]);

        try {
            $updated = $this->preferenceService->update($request->user_id, $request->only([
                'game_id', 'service', 'points', 'duration', 'max_price'
            ]));
            
            return $this->successResponse('User preference updated.', 200, ['data' => $updated]);
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(),500);
        }
    }
}
