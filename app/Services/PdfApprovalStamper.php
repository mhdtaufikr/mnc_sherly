<?php

namespace App\Services;

use App\Models\SalesContract;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class PdfApprovalStamper
{
    public function stamp(SalesContract $salesContract): ?string
    {
        if (! $this->isPdf($salesContract->contract_file_path)) {
            return null;
        }

        $approved = $salesContract->approvals()
            ->where('status', 'Approved')
            ->orderBy('approval_order')
            ->get();

        if ($approved->isEmpty()) {
            return null;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($salesContract->contract_file_path)) {
            return null;
        }

        $source = $disk->path($salesContract->contract_file_path);
        $target = 'contracts/stamped/sales-contract-' . $salesContract->id . '-approved.pdf';

        $disk->makeDirectory('contracts/stamped');

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($source);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);
            $orientation = $size['width'] > $size['height'] ? 'L' : 'P';

            $pdf->AddPage($orientation, [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);
            $this->drawApprovalStamp($pdf, $approved, $size['width'], $size['height']);
        }

        $pdf->Output($disk->path($target), 'F');

        $salesContract->update([
            'stamped_contract_file_path' => $target,
            'stamped_contract_file_name' => $this->stampedFileName($salesContract),
        ]);

        return $target;
    }

    private function drawApprovalStamp(Fpdi $pdf, $approved, float $pageWidth, float $pageHeight): void
    {
        $left = 10;
        $bottom = 10;
        $boxHeight = min(34, max(18, ceil($approved->count() / 3) * 12 + 10));
        $y = $pageHeight - $bottom - $boxHeight;
        $width = $pageWidth - 20;

        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetDrawColor(0, 128, 128);
        $pdf->SetLineWidth(0.25);
        $pdf->Rect($left, $y, $width, $boxHeight, 'DF');

        $pdf->SetTextColor(0, 96, 96);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->SetXY($left + 3, $y + 2);
        $pdf->Cell($width - 6, 4, 'APPROVAL PARAF / INITIAL', 0, 1);

        $columnWidth = $width / 3;
        $startY = $y + 8;

        foreach ($approved->values() as $index => $approval) {
            $column = $index % 3;
            $row = intdiv($index, 3);
            $x = $left + ($column * $columnWidth) + 3;
            $itemY = $startY + ($row * 12);

            $pdf->SetXY($x, $itemY);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetTextColor(0, 120, 120);
            $pdf->Cell(12, 4, $this->initials($approval->approver_name), 0, 0);

            $pdf->SetFont('Arial', '', 6);
            $pdf->SetTextColor(40, 40, 40);
            $pdf->SetXY($x + 12, $itemY);
            $pdf->Cell($columnWidth - 15, 3.5, $approval->approver_name, 0, 1);
            $pdf->SetX($x + 12);
            $pdf->Cell($columnWidth - 15, 3.5, $approval->approved_at?->format('d M Y H:i') ?? '-', 0, 1);
        }
    }

    private function initials(string $name): string
    {
        return collect(preg_split('/\s+/', trim($name)))
            ->filter()
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->take(3)
            ->implode('');
    }

    private function isPdf(?string $path): bool
    {
        return $path && strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'pdf';
    }

    private function stampedFileName(SalesContract $salesContract): string
    {
        $base = pathinfo($salesContract->contract_file_name ?: 'contract.pdf', PATHINFO_FILENAME);

        return $base . '-approved.pdf';
    }
}
