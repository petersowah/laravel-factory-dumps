<?php

namespace PeterSowah\LaravelFactoryDumps\Tests;

use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\ExcelServiceProvider;
use Workbench\App\Models\User;

class ExportableFactoryTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom('../workbench/database/migrations');
        $this->loadLaravelMigrations();
        $this->artisan('migrate', ['--database' => 'testbench'])->run();
        // optimize clear
        $this->artisan('optimize:clear');
        File::cleanDirectory(config('factory-dumps.path'));
    }

    protected function getPackageProviders($app): array
    {
        return [
            ExcelServiceProvider::class,
            // Other service providers if needed
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    public function getEnvironmentSetUp($app): void
    {
        // Set up your database configuration here
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /** @test */
    public function it_can_export_factory_data_to_csv_after_creation(): void
    {
        $users = User::factory()->count(10)->create();
        $csvFile = $users->toCsv();

        $this->assertFileExists($csvFile);
        $this->assertStringContainsString('users.csv', $csvFile);
    }

    /** @test */
    public function it_can_export_factory_data_to_excel_after_creation(): void
    {
        $users = User::factory()->count(10)->create();
        $excelFile = $users->toExcel();

        $this->assertFileExists($excelFile);
        $this->assertStringContainsString('users.xlsx', $excelFile);
    }

    /** @test */
    public function it_can_export_all_users_to_csv_using_static_method(): void
    {
        // Create some users
        User::factory()->count(10)->create();

        // Export using static method
        $csvFile = User::toCsv();

        $this->assertFileExists($csvFile);
        $this->assertStringContainsString('users.csv', $csvFile);
    }

    /** @test */
    public function it_can_export_all_users_to_excel_using_static_method(): void
    {
        // Create some users
        User::factory()->count(10)->create();

        // Export using static method
        $excelFile = User::toExcel();

        $this->assertFileExists($excelFile);
        $this->assertStringContainsString('users.xlsx', $excelFile);
    }

    /** @test */
    public function it_can_export_with_custom_filename(): void
    {
        User::factory()->count(10)->create();

        $excelFile = User::toExcel('custom-users.xlsx');
        $csvFile = User::toCsv('custom-users.csv');

        $this->assertFileExists($excelFile);
        $this->assertFileExists($csvFile);
        $this->assertStringContainsString('custom-users.xlsx', $excelFile);
        $this->assertStringContainsString('custom-users.csv', $csvFile);
    }
}
