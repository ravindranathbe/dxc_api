<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Services\EmployeeService;

it('processes files and returns a csv download', function () {
    Storage::fake('public');

    // Fake uploaded files
    $previousFile = UploadedFile::fake()->create('previous.csv', 10);
    $employeesFile = UploadedFile::fake()->create('employees.csv', 10);

    // Mock service
    $mock = Mockery::mock(EmployeeService::class);
    $this->app->instance(EmployeeService::class, $mock);

    $mock->shouldReceive('processFile')->twice()->andReturn([
        ['Employee_Name' => 'A', 'Employee_EmailID' => 'a@test.com']
    ]);

    $mock->shouldReceive('getSecretChildData')->once()->andReturn([
        [
            'Employee_Name' => 'Employee_Name',
            'Employee_EmailID' => 'Employee_EmailID',
            'Secret_Child_Name' => 'Secret_Child_Name',
            'Secret_Child_EmailID' => 'Secret_Child_EmailID',
        ]
    ]);

    $mock->shouldReceive('exportEmployeesData')->once()->andReturn('csv-content');

    $response = $this->post(route('employee.process'), [
        'previous_year_file' => $previousFile,
        'employees_file' => $employeesFile,
    ]);

    $response->assertStatus(200);

    expect(Storage::disk('public')->exists('secret_santa.csv'))->toBeTrue();
});

it('returns error response when exception occurs', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->create('file.csv', 10);

    $mock = Mockery::mock(EmployeeService::class);
    $this->app->instance(EmployeeService::class, $mock);

    $mock->shouldReceive('processFile')
        ->andThrow(new Exception('Something went wrong'));

    $response = $this->post(route('employee.process'), [
        'previous_year_file' => $file,
        'employees_file' => $file,
    ]);

    $response->assertStatus(500)
        ->assertJsonStructure([
            'message',
            'error',
            'file',
            'line',
            'trace',
        ]);
});
