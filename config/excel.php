<?php

declare(strict_types=1);

use Maatwebsite\Excel\Excel;

return [
    "exports" => [
        "chunk_size" => 1000,
        "pre_calculate_formulas" => false,
        "strict_null_comparison" => false,
        "csv" => [
            "delimiter" => ",",
            "enclosure" => '"',
            "line_ending" => PHP_EOL,
            "use_bom" => false,
            "include_separator_line" => false,
            "excel_compatibility" => false,
            "output_encoding" => "",
        ],
        "properties" => [
            "creator" => "",
            "lastModifiedBy" => "",
            "title" => "",
            "description" => "",
            "subject" => "",
            "keywords" => "",
            "category" => "",
            "manager" => "",
            "company" => "",
        ],
    ],

    "imports" => [
        "read_only" => true,
        "ignore_empty" => false,
        "heading_row" => [
            "formatter" => "slug",
        ],
        "csv" => [
            "delimiter" => ",",
            "enclosure" => '"',
            "escape_character" => "\\",
            "contiguous" => false,
            "input_encoding" => "UTF-8",
        ],
        "properties" => [
            "creator" => "",
            "lastModifiedBy" => "",
            "title" => "",
            "description" => "",
            "subject" => "",
            "keywords" => "",
            "category" => "",
            "manager" => "",
            "company" => "",
        ],
    ],
    "extension_detector" => [
        "xlsx" => Excel::XLSX,
        "xlsm" => Excel::XLSX,
        "xltx" => Excel::XLSX,
        "xltm" => Excel::XLSX,
        "xls" => Excel::XLS,
        "xlt" => Excel::XLS,
        "ods" => Excel::ODS,
        "ots" => Excel::ODS,
        "slk" => Excel::SLK,
        "xml" => Excel::XML,
        "gnumeric" => Excel::GNUMERIC,
        "htm" => Excel::HTML,
        "html" => Excel::HTML,
        "csv" => Excel::CSV,
        "tsv" => Excel::TSV,
        "pdf" => Excel::DOMPDF,
    ],
    "value_binder" => [
        "default" => Maatwebsite\Excel\DefaultValueBinder::class,
    ],

    "cache" => [
        "driver" => "memory",
        "batch" => [
            "memory_limit" => 60000,
        ],
        "illuminate" => [
            "store" => null,
        ],
    ],
    "transactions" => [
        "handler" => "db",
        "db" => [
            "connection" => null,
        ],
    ],

    "temporary_files" => [
        "local_path" => storage_path("framework/cache/laravel-excel"),
        "remote_disk" => null,
        "remote_prefix" => null,
        "force_resync_remote" => null,
    ],
];
