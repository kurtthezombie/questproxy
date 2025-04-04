<?php

namespace App\Exports;

use App\Models\TransactionHistory;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Font;

class TransactionsExport implements FromCollection, WithHeadings, WithStyles
{

    protected $user_id;

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
         return TransactionHistory::whereHas('payment', function ($query) {
            $query->where('payer_id', $this->user_id);
        })
        ->with('payment')
        ->get()
        ->map(function ($transaction) {
            return [
                $transaction->id,
                $transaction->payment->id,
                $transaction->payment->amount,
                $transaction->payment->method,
                $transaction->status,
                $transaction->created_at,
                $transaction->updated_at,
            ];
        });
    }

    public function headings(): array {
        return [
            'Transaction ID',
            'Payment ID',
            'Payment Amount',
            'Payment Method',
            'Transaction Status',
            'Created At',
            'Updated At',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Apply styles to header row (A1:F1)
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold' => true,           // Bold text for headers
                'color' => ['rgb' => 'FFFFFF'],  // White font color
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4CAF50'],  // Green background
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,  // Center align the text
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,    // Vertically center
            ]
        ]);

        // Apply border style for all rows
        $sheet->getStyle('A1:G' . ($sheet->getHighestRow()))
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],  // Black border
                    ]
                ]
            ]);

        // Auto size columns based on content
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}
