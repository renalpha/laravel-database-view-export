<?php

namespace EUR\RSM\DatabaseViewExport\Exports;

use EUR\RSM\DatabaseViewExport\Models\Export;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Excel;

class ViewExport implements FromCollection, WithHeadings, WithStrictNullComparison
{
    use Exportable;

    private $exportType = Excel::XLSX;

    public function __construct(private Export $export)
    {
    }

    public function record(): Export
    {
        return $this->export;
    }

    public function label(): string
    {
        return $this->export->name;
    }

    public function url(): string
    {
        return route('database-view-export.export', $this->export->slug);
    }

    public function collection(): Collection
    {
        return DB::table($this->export->view_name)->select()->get();
    }

    public function headings(): array
    {
        return DB::getSchemaBuilder()->getColumnListing($this->export->view_name);
    }

    public function isView(): bool
    {
        return DB::getSchemaBuilder()->hasView($this->export->view_name);
    }

    public function filename(): string
    {
        return $this->export->slug . '_' . now()->toDateTimeString() . '.' . strtolower($this->exportType);
    }
}
