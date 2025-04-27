<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentsPaidExport implements FromCollection, WithHeadings, WithStyles, WithMapping
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
        return Payment::where('payer_id', $this->user_id)
                       ->where('status', 'paid')
                       ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Amount',
            'Method',
            'Details',
            'Status',
            'Booking ID',
            'Transaction ID',
            'Payment Link',
            'Created At',
            'Updated At'
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->id,
            $payment->amount,
            $payment->method,
            $payment->details,
            $payment->status,
            $payment->booking_id,
            $payment->transaction_id,
            $payment->payment_link,
            $payment->created_at->format('Y-m-d H:i'),
            $payment->updated_at->format('Y-m-d H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Apply styles to header row (A1:J1)
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold' => true,           // Bold text for headers
                'color' => ['rgb' => 'FFFFFF'],  // White font color
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3D6FBF'],  // Green background
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,  // Center align the text
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,    // Vertically center
            ]
        ]);

        // Apply border style for all rows
        $sheet->getStyle('A1:J' . ($sheet->getHighestRow()))
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],  // Black border
                    ]
                ]
            ]);

        // Auto size columns based on content
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}
