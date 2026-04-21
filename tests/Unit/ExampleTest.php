<?php

use App\Services\EmployeeService;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    $service = new EmployeeService();

    // share it with tests
    test()->service = $service;
});

it('generates secret child data with correct structure', function () {
    $employees = [
        ['Employee_Name' => 'A', 'Employee_EmailID' => 'a@test.com'],
        ['Employee_Name' => 'B', 'Employee_EmailID' => 'b@test.com'],
        ['Employee_Name' => 'C', 'Employee_EmailID' => 'c@test.com'],
    ];

    $previous = [];

    $result = $this->service->getSecretChildData($previous, $employees);

    // First row is header
    expect($result[0])->toBe([
        'Employee_Name' => 'Employee_Name',
        'Employee_EmailID' => 'Employee_EmailID',
        'Secret_Child_Name' => 'Secret_Child_Name',
        'Secret_Child_EmailID' => 'Secret_Child_EmailID',
    ]);

    // Should have same number of employees + header
    expect($result)->toHaveCount(count($employees) + 1);
});

it('does not assign employee to themselves', function () {
    $employees = [
        ['Employee_Name' => 'A', 'Employee_EmailID' => 'a@test.com'],
        ['Employee_Name' => 'B', 'Employee_EmailID' => 'b@test.com'],
    ];

    $result = $this->service->getSecretChildData([], $employees);

    collect($result)->skip(1)->each(function ($row) {
        expect($row['Employee_EmailID'])
            ->not->toBe($row['Secret_Child_EmailID']);
    });
});

it('does not reuse secret child emails', function () {
    $employees = [
        ['Employee_Name' => 'A', 'Employee_EmailID' => 'a@test.com'],
        ['Employee_Name' => 'B', 'Employee_EmailID' => 'b@test.com'],
        ['Employee_Name' => 'C', 'Employee_EmailID' => 'c@test.com'],
    ];

    $result = $this->service->getSecretChildData([], $employees);

    $assigned = collect($result)->skip(1)->pluck('Secret_Child_EmailID');

    expect($assigned)->toHaveCount($assigned->unique()->count());
});

it('does not assign previous year secret child', function () {
    $employees = [
        ['Employee_Name' => 'A', 'Employee_EmailID' => 'a@test.com'],
        ['Employee_Name' => 'B', 'Employee_EmailID' => 'b@test.com'],
        ['Employee_Name' => 'C', 'Employee_EmailID' => 'c@test.com'],
    ];

    $previous = [
        [
            'Employee_EmailID' => 'a@test.com',
            'Secret_Child_EmailID' => 'b@test.com',
        ],
    ];

    $result = $this->service->getSecretChildData($previous, $employees);

    $rowA = collect($result)->firstWhere('Employee_EmailID', 'a@test.com');

    expect($rowA['Secret_Child_EmailID'])->not->toBe('b@test.com');
});

it('returns only header if assignment fails', function () {
    // Edge case: impossible assignment (only 1 employee)
    $employees = [
        ['Employee_Name' => 'A', 'Employee_EmailID' => 'a@test.com'],
    ];

    $result = $this->service->getSecretChildData([], $employees);

    expect($result)->toHaveCount(1); // only header
});
