<?php 

namespace App\Services;

use App\Models\Report;
use App\Models\User;

class ReportService {
    
    protected $report;
    protected $user;

    public function __construct(Report $report, User $user)
    {
        $this->report = $report;
        $this->user = $user;
    }

    public function findById($id) {
        return $this->report->findOrFail($id);
    }

    public function create($data){
        //get user id from the username
        $reported_user = $this->getUserIdByUsername($data['reported_user']);
        //set reporter id
        $reporting_user = auth()->user()->id;

        if ($reported_user == $reporting_user) {
            throw new \Exception("You cannot report yourself!");
        }

        $formData = [
            'reason' => $data['reason'],
            'reported_user' => $reported_user,
            'reporter_user' => $reporting_user,
        ];

        $created = $this->report->create($formData);

        if(!$created) {
            throw new \Exception("Failed to create report");
        }

        return $created;
    }   
    
    public function getUserIdByUsername($username)
    {
        $user = $this->user->where('username', $username)->first();

        if (!$user) {
            throw new \Exception("User not found");
        }

        return $user->id;
    }
}