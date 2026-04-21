<?php

namespace App\Services;

use App\Helpers\File;
use Illuminate\Support\Facades\Log;

class EmployeeService
{
    /**
     * Process file
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @return array
     */
    public function processFile($file)
    {
        if ($file) {
            return File::processCsv($file);
        }

        return [];
    }

    /**
     * Export employees data to CSV
     * 
     * @param array $employeesData
     * @return string
     */
    public function exportEmployeesData($employeesData)
    {
        return File::exportCsv($employeesData);
    }

    /**
     * Get secret child data
     * 
     * @param array $previousYearData
     * @param array $employeesData
     * @return array
     */
    public function getSecretChildData($previousYearData, $employeesData)
    {
        $employeesData = collect($employeesData);
        $previousYearData = collect($previousYearData);
        $secretChildData = [
            [
                'Employee_Name' => 'Employee_Name',
                'Employee_EmailID' => 'Employee_EmailID',
                'Secret_Child_Name' => 'Secret_Child_Name',
                'Secret_Child_EmailID' => 'Secret_Child_EmailID',
            ]
        ];
        $secretChildEmailTemp = [];

        $secretChildDataTemp = $employeesData->map(function ($employee) use ($employeesData, $previousYearData, &$secretChildEmailTemp) {
            $employeeEmail = $employee['Employee_EmailID'];

            // get previous year secret child email id
            $previousYearSecretChildEmailId = $previousYearData->where('Employee_EmailID', $employeeEmail)->first()['Secret_Child_EmailID'] ?? null;

            // get all users except current user 
            $restOfUsers = collect($employeesData)->where('Employee_EmailID', '!=', $employeeEmail);

            // get a random user from restOfUsers which is not in secretChildEmailTemp and previous year secret child email id
            $randomUser = $restOfUsers->whereNotIn('Employee_EmailID', $secretChildEmailTemp)->whereNotIn('Employee_EmailID', $previousYearSecretChildEmailId)->shuffle()->first();

            if (empty($randomUser)) {
                return false;
            }

            $secretChildEmailTemp[] = $randomUser['Employee_EmailID'];

            return [
                'Employee_Name' => $employee['Employee_Name'],
                'Employee_EmailID' => $employee['Employee_EmailID'],
                'Secret_Child_Name' => $randomUser['Employee_Name'],
                'Secret_Child_EmailID' => $randomUser['Employee_EmailID'],
            ];
        });

        // filter out false values
        $secretChildData = array_merge($secretChildData, $secretChildDataTemp->filter(fn($value) => $value !== false)->toArray());

        return $secretChildData;
    }
}
