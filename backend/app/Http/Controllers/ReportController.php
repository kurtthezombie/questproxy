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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //validate the form
        $validatedData = $request->validate([
            'reason' => 'required|string',
            'reported_user_id' => 'required' 
        ]);

        //add authenticated user's id 
        $validatedData['reporting_user_id'] = $request->user()->id;

        //insert in reports table
        try {
            $reported =  $this->reportService->create($validatedData);

            return $this->successResponse('Report submitted.',201,['report' => $reported]);
        } catch (Exception $e){
            return $this->failedResponse($e->getMessage(),500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $report = $this->reportService->find($id);

            return $this->successResponse(
                'Report retrieved.',
                200,
                ['report' => $report],);
        } catch (ModelNotFoundException $e) {
            return $this->failedResponse("Error: " . $e->getMessage(),404);
        } 
        catch (Exception $e){
            return $this->failedResponse($e->getMessage(),500);
        }
    }
}
