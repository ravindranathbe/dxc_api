<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeProcessRequest;
use App\Services\EmployeeService;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    /**
     * Constructor
     * 
     * @param \App\Services\EmployeeService $employeeService
     */
    public function __construct(protected EmployeeService $employeeService) {}

    /**
     * Process employee data
     * 
     * @param \App\Http\Requests\EmployeeProcessRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function process(EmployeeProcessRequest $request)
    {
        try {
            $previousYearFile = $request->file('previous_year_file');
            $employeesFile = $request->file('employees_file');

            $previousYearData = $this->employeeService->processFile($previousYearFile);

            $employeesData = $this->employeeService->processFile($employeesFile);

            $secretChildData = $this->employeeService->getSecretChildData($previousYearData, $employeesData);

            Storage::disk('public')->put(
                'secret_santa.csv',
                $this->employeeService->exportEmployeesData($secretChildData)
            );

            return response()->download(
                Storage::disk('public')->path('secret_santa.csv')
            );
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error processing employee data',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }
}
