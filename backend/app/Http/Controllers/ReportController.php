<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Services\ReportService;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use ApiResponseTrait;

    protected $reportService;

    public function __construct(ReportService $reportService){
        $this->reportService = $reportService;
    }

    public function index()
    {
        try {
            $user_id = auth()->user()->id;
            $reports = $this->reportService->fetchReportsByUser($user_id);

            if ($reports->isEmpty()) {
                return $this->successResponse(
                    'No reports found.',
                    200,
                    ['reports' => []]
                );
            }

            return $this->successResponse(
                'Reports retrieved.',
                200,
                ['reports' => $reports]
            );
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            //validate the form
            $validatedData = $request->validate([
                'reason' => 'required|string',
                'reported_user' => 'required|string|exists:users,username',
            ]);
            $reported = $this->reportService->create($validatedData);

            return $this->successResponse('Report submitted.', 201, ['report' => $reported]);
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $report = $this->reportService->findById($id);

            return $this->successResponse(
                'Report retrieved.',
                200,
                ['report' => $report],);
        } catch (ModelNotFoundException $e) {
            return $this->failedResponse("Report {$id} is not found.",404);
        }
        catch (Exception $e){
            return $this->failedResponse($e->getMessage(),500);
        }
    }
}
