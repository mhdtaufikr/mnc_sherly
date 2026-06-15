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
        $margin = 6;
        $visibleApprovals = $approved->values()->take(10);
        $columns = 5;
        $chipWidth = 7.8;
        $chipHeight = 4.3;
        $headerHeight = 3.6;
        $rows = max(1, (int) ceil($visibleApprovals->count() / $columns));
        $width = ($columns * $chipWidth) + 4;
        $boxHeight = $headerHeight + ($rows * $chipHeight) + 2.2;
        $left = $pageWidth - $margin - $width;
        $y = $pageHeight - $margin - $boxHeight;

        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetDrawColor(0, 140, 140);
        $pdf->SetLineWidth(0.15);
        $pdf->Rect($left, $y, $width, $boxHeight, 'D');

        $pdf->SetTextColor(0, 115, 115);
        $pdf->SetFont('Arial', 'B', 5.5);
        $pdf->SetXY($left + 1.4, $y + 0.9);
        $pdf->Cell($width - 2.8, 2.5, 'APPROVED', 0, 1, 'R');

        $startX = $left + 1.8;
        $startY = $y + $headerHeight + 0.9;

        foreach ($visibleApprovals as $index => $approval) {
            $column = $index % $columns;
            $row = intdiv($index, $columns);
            $itemX = $startX + ($column * $chipWidth);
            $itemY = $startY + ($row * $chipHeight);

            $pdf->SetDrawColor(0, 140, 140);
            $pdf->Rect($itemX, $itemY + 0.7, 2.4, 2.4, 'D');
            $pdf->Line($itemX + 0.45, $itemY + 1.9, $itemX + 1.0, $itemY + 2.55);
            $pdf->Line($itemX + 1.0, $itemY + 2.55, $itemX + 2.05, $itemY + 1.2);

            $pdf->SetTextColor(0, 112, 112);
            $pdf->SetFont('Arial', 'B', 5.2);
            $pdf->SetXY($itemX + 2.9, $itemY + 0.65);
            $pdf->Cell($chipWidth - 2.9, 2.6, $this->initials($approval->approver_name), 0, 0);
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
