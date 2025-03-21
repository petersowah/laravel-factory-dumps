<?php

namespace Tests;

use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\ExcelServiceProvider;
use Workbench\App\Models\User;

class ExportableFactoryTest extends TestCase
{
    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom('../workbench/database/migrations');
        $this->loadLaravelMigrations();
        $this->artisan('migrate', ['--database' => 'testbench'])->run();
        $this->artisan('optimize:clear');

        // Ensure the export directories exist and are writable
        $basePath = config('factory-dumps.path');
        File::ensureDirectoryExists($basePath);
        File::ensureDirectoryExists($basePath.'/dumps/excel');
        File::ensureDirectoryExists($basePath.'/dumps/csv');

        // Clean up any existing files
        File::cleanDirectory($basePath);
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

        // Set up a writable path for factory dumps
        $dumpPath = __DIR__.'/temp';
        $app['config']->set('factory-dumps.path', $dumpPath);

        // Configure the filesystem for Excel exports
        $app['config']->set('filesystems.disks.default', [
            'driver' => 'local',
            'root' => $dumpPath,
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
        User::factory()->count(10)->create();

        $csvFile = User::toCsv();

        $this->assertFileExists($csvFile);
        $this->assertStringContainsString('users.csv', $csvFile);
    }

    /** @test */
    public function it_can_export_all_users_to_excel_using_static_method(): void
    {
        User::factory()->count(10)->create();

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

    protected function tearDown(): void
    {
        // Clean up the temporary directory
        if (file_exists(__DIR__.'/temp')) {
            File::deleteDirectory(__DIR__.'/temp');
        }

        parent::tearDown();
    }
}
