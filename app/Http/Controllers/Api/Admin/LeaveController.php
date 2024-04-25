<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\LeaveService;
use Illuminate\Http\Request;
use function App\errorLogs;

class LeaveController extends Controller
{
    protected $leaveService;
    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    /**
     * Retrieves all leaves.
     *
     * Method: GET
     * @return \Illuminate\Http\JsonResponse JSON response containing all leaves.
     */
    public function index() {
        try {
            $leaves = $this->leaveService->getAllLeaves();
            return response()->request_response(200, true, "", $leaves);
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__,$ex->getMessage());
        }
    }
    
    /**
     * Approve a leave request.
     *
     * @param \Illuminate\Http\Request $request The HTTP request object.
     * @param int $id The ID of the leave request to be approved.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the approval status and data.
     */
    public function leaveApproved(Request $request, $id) {
        try {
            $status = $request->input('status');
            $leave = $this->leaveService->leaveApproved($status, $id);
            return response()->request_response(200, true, "The leave application is Approved.", $leave);
        } catch (\Exception $ex) {
            return errorLogs(__METHOD__,__LINE__,$ex->getMessage());
        }
    }
}
