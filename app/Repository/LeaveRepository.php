<?php

namespace App\Repository;

use App\Models\Leave;
use App\Repository\Interface\leaveRepositoryInterface;

use function App\errorLogs;

class leaveRepository implements leaveRepositoryInterface{

    /**
     * Retrieve all leave records with status other than 'Approved'.
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of leave records.
     */
    public function leaves() {
        try {
            return Leave::where('status', '!=', "Approved")->get();
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__, __LINE__, $ex->getMessage());
        }
    }

    /**
    * Approve a leave application.
    *
    * @param array $data Data containing the new status for the leave application.
    * @param int $id ID of the leave application to be approved.
    * @return \Illuminate\Http\JsonResponse JSON response indicating the status of the approval process.
    */
    public function approveLeave($data, $id)
    {
        try {
            $leave = Leave::findOrFail($id);
    
            $statusCode = 200;
            $success = true;
            $message = '';
            $responseData = null;
        
            if (!$leave) {
                $statusCode = 404;
                $success = false;
                $message = 'Leave Record not found';
            } elseif ($leave->status == 'approved') {
                $message = 'This leave has already been approved';
            } elseif ($leave->status == 'pending') {
                $leave->update(['status' => $data]);
                $message = 'The leave application was Approved.';
                $responseData = $leave;
            }
        
            return response()->request_response($statusCode, $success, $message, $responseData);
        } catch (\Exception $ex) {
            errorLogs(__METHOD__,__LINE__,$ex->getMessage());
        }
    }
}