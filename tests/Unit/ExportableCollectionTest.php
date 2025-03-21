<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use PeterSowah\LaravelFactoryDumps\Collections\ExportableCollection;
use PeterSowah\LaravelFactoryDumps\Exports\ExportFactory;
use Tests\TestCase;

class TestModel extends Model
{
    protected $table = 'test_table';

    protected $fillable = ['name', 'email', 'age'];
}

class ExportableCollectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Ensure directories exist
        File::ensureDirectoryExists(database_path('dumps/csv'));
        File::ensureDirectoryExists(config('factory-dumps.path').'/dumps/excel');

        // Configure the filesystem for Excel exports
        config(['filesystems.disks.local' => [
            'driver' => 'local',
            'root' => config('factory-dumps.path'),
        ]]);

        // Configure Excel to use the local disk
        config(['excel.exports.disk' => 'local']);
    }

    protected function tearDown(): void
    {
        // Clean up test files
        $files = [
            database_path('dumps/csv/test.csv'),
            database_path('dumps/csv/test_table.csv'),
            database_path('dumps/csv/export.csv'),
            config('factory-dumps.path').'/dumps/excel/test.xlsx',
            config('factory-dumps.path').'/dumps/excel/test_table.xlsx',
            config('factory-dumps.path').'/dumps/excel/export.xlsx',
        ];

        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

        parent::tearDown();
    }

    /** @test */
    public function it_can_pluck_specific_columns()
    {
        $collection = new ExportableCollection([
            ['id' => 1, 'name' => 'John', 'email' => 'john@example.com'],
            ['id' => 2, 'name' => 'Jane', 'email' => 'jane@example.com'],
        ]);

        $result = $collection->pluck(['name', 'email']);

        $this->assertEquals([
            ['name' => 'John', 'email' => 'john@example.com'],
            ['name' => 'Jane', 'email' => 'jane@example.com'],
        ], $result->toArray());
    }

    /** @test */
    public function it_can_pluck_a_single_column()
    {
        $collection = new ExportableCollection([
            ['id' => 1, 'name' => 'John', 'email' => 'john@example.com'],
            ['id' => 2, 'name' => 'Jane', 'email' => 'jane@example.com'],
        ]);

        $result = $collection->pluck('name');

        $this->assertEquals([
            ['name' => 'John'],
            ['name' => 'Jane'],
        ], $result->toArray());
    }

    /** @test */
    public function it_can_pluck_with_custom_column_names()
    {
        $collection = new ExportableCollection([
            ['id' => 1, 'name' => 'John', 'email' => 'john@example.com', 'age' => 30],
            ['id' => 2, 'name' => 'Jane', 'email' => 'jane@example.com', 'age' => 25],
        ]);

        $result = $collection->pluck([
            'name' => 'Full Name',
            'email' => 'Email Address',
            'age' => 'Age',
        ]);

        $this->assertEquals([
            ['Full Name' => 'John', 'Email Address' => 'john@example.com', 'Age' => 30],
            ['Full Name' => 'Jane', 'Email Address' => 'jane@example.com', 'Age' => 25],
        ], $result->toArray());
    }

    /** @test */
    public function it_can_export_collection_to_csv_file()
    {
        $collection = new ExportableCollection([
            ['id' => 1, 'name' => 'John', 'email' => 'john@example.com'],
            ['id' => 2, 'name' => 'Jane', 'email' => 'jane@example.com'],
        ]);

        $filePath = $collection->toCsv('test.csv');

        $this->assertEquals(database_path('dumps/csv/test.csv'), $filePath);
        $this->assertFileExists($filePath);

        $csvContent = file_get_contents($filePath);
        $this->assertStringContainsString('id,name,email', $csvContent);
        $this->assertStringContainsString('1,John,john@example.com', $csvContent);
        $this->assertStringContainsString('2,Jane,jane@example.com', $csvContent);
    }

    /** @test */
    public function it_uses_model_table_name_when_no_filename_provided_for_csv()
    {
        $model = new TestModel(['name' => 'John', 'email' => 'john@example.com']);
        $collection = new ExportableCollection([$model]);

        $filePath = $collection->toCsv();

        $this->assertEquals(database_path('dumps/csv/test_table.csv'), $filePath);
    }

    /** @test */
    public function it_uses_export_as_default_name_for_array_data_in_csv()
    {
        $collection = new ExportableCollection([
            ['name' => 'John', 'email' => 'john@example.com'],
        ]);

        $filePath = $collection->toCsv();

        $this->assertEquals(database_path('dumps/csv/export.csv'), $filePath);
    }

    /** @test */
    public function it_can_export_collection_to_excel_file()
    {
        $collection = new ExportableCollection([
            ['id' => 1, 'name' => 'John', 'email' => 'john@example.com'],
            ['id' => 2, 'name' => 'Jane', 'email' => 'jane@example.com'],
        ]);

        Excel::fake();

        $filePath = $collection->toExcel('test.xlsx');

        Excel::assertStored(
            'dumps/excel/test.xlsx',
            function (ExportFactory $export) {
                return true;
            }
        );

        $this->assertEquals(config('factory-dumps.path').'/dumps/excel/test.xlsx', $filePath);
    }

    /** @test */
    public function it_uses_model_table_name_when_no_filename_provided_for_excel()
    {
        $model = new TestModel(['name' => 'John', 'email' => 'john@example.com']);
        $collection = new ExportableCollection([$model]);

        Excel::fake();

        $filePath = $collection->toExcel();

        Excel::assertStored(
            'dumps/excel/test_table.xlsx',
            function (ExportFactory $export) {
                return true;
            }
        );

        $this->assertEquals(config('factory-dumps.path').'/dumps/excel/test_table.xlsx', $filePath);
    }

    /** @test */
    public function it_uses_export_as_default_name_for_array_data_in_excel()
    {
        $collection = new ExportableCollection([
            ['name' => 'John', 'email' => 'john@example.com'],
        ]);

        Excel::fake();

        $filePath = $collection->toExcel();

        Excel::assertStored(
            'dumps/excel/export.xlsx',
            function (ExportFactory $export) {
                return true;
            }
        );

        $this->assertEquals(config('factory-dumps.path').'/dumps/excel/export.xlsx', $filePath);
    }
}
