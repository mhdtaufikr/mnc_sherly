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
        $margin = 5;
        $visibleApprovals = $approved->values()->take(10);
        $columns = 5;
        $chipWidth = 6.4;
        $chipHeight = 3.6;
        $headerHeight = 3.2;
        $rows = max(1, (int) ceil($visibleApprovals->count() / $columns));
        $width = ($columns * $chipWidth) + 3;
        $boxHeight = $headerHeight + ($rows * $chipHeight) + 1.5;
        $left = $pageWidth - $margin - $width;
        $y = $margin;

        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetDrawColor(215, 235, 235);
        $pdf->SetLineWidth(0.1);
        $pdf->Rect($left, $y, $width, $boxHeight, 'DF');

        $pdf->SetTextColor(0, 115, 115);
        $pdf->SetFont('Arial', 'B', 4.7);
        $pdf->SetXY($left + 1.2, $y + 0.75);
        $pdf->Cell($width - 2.4, 2.2, 'APPROVED', 0, 1, 'R');

        $startX = $left + 1.3;
        $startY = $y + $headerHeight + 0.35;

        foreach ($visibleApprovals as $index => $approval) {
            $column = $index % $columns;
            $row = intdiv($index, $columns);
            $itemX = $startX + ($column * $chipWidth);
            $itemY = $startY + ($row * $chipHeight);

            $pdf->SetDrawColor(0, 140, 140);
            $pdf->Rect($itemX, $itemY + 0.55, 2.0, 2.0, 'D');
            $pdf->Line($itemX + 0.35, $itemY + 1.55, $itemX + 0.82, $itemY + 2.05);
            $pdf->Line($itemX + 0.82, $itemY + 2.05, $itemX + 1.72, $itemY + 0.95);

            $pdf->SetTextColor(0, 112, 112);
            $pdf->SetFont('Arial', 'B', 4.8);
            $pdf->SetXY($itemX + 2.45, $itemY + 0.45);
            $pdf->Cell($chipWidth - 2.45, 2.4, $this->initials($approval->approver_name), 0, 0);
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
