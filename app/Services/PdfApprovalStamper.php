<?php

namespace App\Services;

use App\Models\SalesContract;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class PdfApprovalStamper
{
    public function stamp(SalesContract $salesContract): ?string
    {
        if (! class_exists(Fpdi::class)) {
            report('PDF approval stamping skipped: setasign/fpdi is not installed. Run composer install on the server.');

            return null;
        }

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
        $margin = 8;
        $width = min(58, $pageWidth - ($margin * 2));
        $chipHeight = 5.2;
        $headerHeight = 5.5;
        $visibleRows = min($approved->count(), 8);
        $boxHeight = $headerHeight + ($visibleRows * $chipHeight) + 3;
        $left = $pageWidth - $margin - $width;
        $y = $pageHeight - $margin - $boxHeight;

        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetDrawColor(0, 120, 120);
        $pdf->SetLineWidth(0.25);
        $pdf->Rect($left, $y, $width, $boxHeight, 'DF');

        $pdf->SetFillColor(0, 128, 128);
        $pdf->Rect($left, $y, $width, $headerHeight, 'F');
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 6.5);
        $pdf->SetXY($left + 2, $y + 1.4);
        $pdf->Cell($width - 4, 3, 'APPROVED INITIALS', 0, 1, 'C');

        $startY = $y + $headerHeight + 1.6;
        $pdf->SetFont('Arial', '', 5.2);

        foreach ($approved->values()->take(8) as $index => $approval) {
            $itemY = $startY + ($index * $chipHeight);
            $initialWidth = 9;

            $pdf->SetFillColor(232, 247, 247);
            $pdf->SetDrawColor(160, 210, 210);
            $pdf->Rect($left + 1.5, $itemY, $width - 3, 4.4, 'DF');

            $pdf->SetTextColor(0, 112, 112);
            $pdf->SetFont('Arial', 'B', 6.5);
            $pdf->SetXY($left + 2.3, $itemY + 0.8);
            $pdf->Cell($initialWidth, 2.8, $this->initials($approval->approver_name), 0, 0);

            $pdf->SetFont('Arial', '', 6);
            $pdf->SetTextColor(45, 45, 45);
            $pdf->SetXY($left + 2.3 + $initialWidth, $itemY + 0.8);
            $pdf->Cell($width - $initialWidth - 5, 2.8, $this->shortName($approval->approver_name) . ' - ' . ($approval->approved_at?->format('d/m/y H:i') ?? '-'), 0, 1);
        }

        if ($approved->count() > 8) {
            $pdf->SetTextColor(90, 90, 90);
            $pdf->SetFont('Arial', '', 5.5);
            $pdf->SetXY($left + 2, $pageHeight - $margin - 4);
            $pdf->Cell($width - 4, 2.8, '+' . ($approved->count() - 8) . ' more approvals', 0, 0, 'R');
        }
    }

    private function shortName(string $name): string
    {
        $name = trim($name);

        return strlen($name) > 18 ? substr($name, 0, 17) . '.' : $name;
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
