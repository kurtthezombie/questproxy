<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use ApiResponseTrait;
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //validate the form
        $request->validate([
            'reason' => 'required|string',
            'reported_user_id' => 'required' 
        ]);
        //take authenticated user
        $reporting_user_id = $request->user()->id;
        //insert in reports table
        try {
            $reported = Report::create([
                'reason' => $request->reason,
                'status' => 'pending',
                'reporting_user_id' => $reporting_user_id,
                'reported_user_id' => $request->reported_user_id, //take the id of the reported
            ]);
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
            $report = Report::findOrFail($id);
        } catch (Exception $e){
            return $this->failedResponse($e->getMessage(),404);
        }

        return $this->successResponse(
            'Report retrieved.',
            200,
            ['report' => $report],
        );
    }
}
