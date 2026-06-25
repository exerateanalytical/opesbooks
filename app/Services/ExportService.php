<?php

namespace App\Services;

use App\Exports\GenericExport;
use App\Models\Company;
use App\Models\Customer;
use App\Models\CustomerInvoice;
use App\Models\JournalEntry;
use App\Models\Supplier;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    public const TYPES = ['invoices', 'clients', 'suppliers', 'journal'];

    /** Resolve [headings, rows] for an export type, scoped to the company. */
    public function dataset(string $type, Company $company): array
    {
        $cid = $company->id;
        return match ($type) {
            'invoices' => [
                ['N° Facture', 'Client', 'Date', 'Échéance', 'HT (XAF)', 'TVA (XAF)', 'TTC (XAF)', 'Statut'],
                CustomerInvoice::where('company_id', $cid)->with('customer')->latest('invoice_date')->get()
                    ->map(fn ($i) => [
                        $i->invoice_number, $i->customer?->name, optional($i->invoice_date)->format('Y-m-d'),
                        optional($i->due_date)->format('Y-m-d'), $i->amount_ht, $i->tva_amount, $i->amount_ttc, $i->status,
                    ])->toArray(),
            ],
            'clients' => [
                ['Nom', 'Email', 'Téléphone', 'NIU'],
                Customer::where('company_id', $cid)->get()
                    ->map(fn ($c) => [$c->name, $c->email, $c->phone, $c->niu])->toArray(),
            ],
            'suppliers' => [
                ['Nom', 'Email', 'Téléphone', 'NIU'],
                Supplier::where('company_id', $cid)->get()
                    ->map(fn ($s) => [$s->name, $s->email ?? '', $s->phone ?? '', $s->niu ?? ''])->toArray(),
            ],
            'journal' => [
                ['Réf', 'Date', 'Libellé', 'Type'],
                JournalEntry::where('company_id', $cid)->latest('id')->limit(5000)->get()
                    ->map(fn ($e) => [$e->reference_id ?? $e->id, optional($e->posting_date)->format('Y-m-d'), $e->memo ?? '', $e->posting_type ?? ''])->toArray(),
            ],
            default => throw new \InvalidArgumentException("Unknown export type: {$type}"),
        };
    }

    public function download(string $type, string $format, Company $company)
    {
        [$headings, $rows] = $this->dataset($type, $company);
        $base = $type . '_' . now()->format('Y-m-d');

        return match ($format) {
            'xlsx' => Excel::download(new GenericExport($rows, $headings), "{$base}.xlsx"),
            'csv'  => $this->streamCsv($headings, $rows, "{$base}.csv"),
            'pdf'  => $this->pdf($headings, $rows, $type, $company, "{$base}.pdf"),
            default => throw new \InvalidArgumentException("Unknown format: {$format}"),
        };
    }

    private function streamCsv(array $headings, array $rows, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($headings, $rows) {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel
            fputcsv($out, $headings);
            foreach ($rows as $r) {
                fputcsv($out, $r);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function pdf(array $headings, array $rows, string $type, Company $company, string $filename)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.generic', compact('headings', 'rows', 'type', 'company'))
            ->setPaper('a4', 'landscape');
        return $pdf->download($filename);
    }
}
