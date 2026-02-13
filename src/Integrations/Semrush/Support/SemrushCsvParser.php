<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Semrush\Support;

use RuntimeException;

final class SemrushCsvParser
{
    /**
     * @return array{
     *     headers: array<int, string>,
     *     rows: array<int, array<string, string>>,
     *     rowCount: int
     * }
     */
    public static function parse(string $csv): array
    {
        if (trim($csv) === '') {
            return [
                'headers' => [],
                'rows' => [],
                'rowCount' => 0,
            ];
        }

        $headerLine = self::firstNonEmptyLine($csv);

        if ($headerLine === null) {
            return [
                'headers' => [],
                'rows' => [],
                'rowCount' => 0,
            ];
        }

        $delimiter = self::detectDelimiter($headerLine);
        $rows = self::parseRows($csv, $delimiter);

        if ($rows === []) {
            return [
                'headers' => [],
                'rows' => [],
                'rowCount' => 0,
            ];
        }

        $headers = array_map(
            static fn (?string $header): string => trim((string) $header),
            array_shift($rows)
        );

        self::assertNoDuplicateHeaders($headers);

        $headerCount = count($headers);
        $parsedRows = [];

        foreach ($rows as $index => $row) {
            if (self::isEmptyRow($row)) {
                continue;
            }

            if (count($row) !== $headerCount) {
                throw new RuntimeException(sprintf(
                    'Malformed Semrush CSV row at line %d. Expected %d columns, got %d.',
                    $index + 2,
                    $headerCount,
                    count($row)
                ));
            }

            $values = array_map(
                static fn (?string $value): string => (string) ($value ?? ''),
                $row
            );

            /** @var array<string, string> $mappedRow */
            $mappedRow = array_combine($headers, $values);
            $parsedRows[] = $mappedRow;
        }

        return [
            'headers' => $headers,
            'rows' => $parsedRows,
            'rowCount' => count($parsedRows),
        ];
    }

    private static function detectDelimiter(string $headerLine): string
    {
        $headerLine = self::stripBom($headerLine);

        $semicolonColumns = count(str_getcsv($headerLine, ';', escape: '\\'));
        $commaColumns = count(str_getcsv($headerLine, ',', escape: '\\'));

        if ($commaColumns > $semicolonColumns) {
            return ',';
        }

        return ';';
    }

    /**
     * @return array<int, array<int, string|null>>
     */
    private static function parseRows(string $csv, string $delimiter): array
    {
        $handle = fopen('php://temp', 'rb+');

        if ($handle === false) {
            throw new RuntimeException('Failed to open temporary stream for Semrush CSV parsing.');
        }

        fwrite($handle, $csv);
        rewind($handle);

        $rows = [];

        while (($row = fgetcsv($handle, 0, $delimiter, escape: '\\')) !== false) {
            if ($row === [null]) {
                continue;
            }

            $rows[] = $row;
        }

        fclose($handle);

        if ($rows !== []) {
            $rows[0][0] = self::stripBom((string) $rows[0][0]);
        }

        return $rows;
    }

    private static function firstNonEmptyLine(string $csv): ?string
    {
        $lines = preg_split('/\r\n|\n|\r/', $csv) ?: [];

        foreach ($lines as $line) {
            if (trim($line) !== '') {
                return $line;
            }
        }

        return null;
    }

    /**
     * @param  array<int, string>  $headers
     */
    private static function assertNoDuplicateHeaders(array $headers): void
    {
        if (count($headers) === count(array_unique($headers))) {
            return;
        }

        throw new RuntimeException('Malformed Semrush CSV header: duplicate column names detected.');
    }

    /**
     * @param  array<int, string|null>  $row
     */
    private static function isEmptyRow(array $row): bool
    {
        return array_all($row, fn($value): bool => trim((string) $value) === '');
    }

    private static function stripBom(string $value): string
    {
        return preg_replace('/^\xEF\xBB\xBF/', '', $value) ?? $value;
    }
}
