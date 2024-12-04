<?php 

namespace App\Services;

use App\Models\Report;

class ReportService {
    
    protected $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    public function find($id) {
        return $this->report->findOrFail($id);
    }

    public function create($data){
        $created = $this->report->create($data);

        if(!$created) {
            throw new \Exception("Failed to create report");
        }

        return $created;
    }
}