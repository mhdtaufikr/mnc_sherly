<?php

namespace App\Http\Controllers;

use App\Models\SalesContractApproval;
use App\Models\SalesContract;
use App\Models\User;
use App\Services\PdfApprovalStamper;
use App\Support\SalesApprovalRoute;
use Illuminate\Http\Request;

class SalesContractApprovalController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        abort_unless(in_array($user->username, SalesApprovalRoute::usernames(), true), 403);

        $this->ensureApprovalRoutes();

        $approvals = SalesContractApproval::query()
            ->with(['salesContract.approvals'])
            ->where('approver_username', $user->username)
            ->whereHas('salesContract')
            ->latest()
            ->paginate(15);

        return view('approvals.index', compact('approvals'));
    }

    public function approve(SalesContractApproval $approval, PdfApprovalStamper $stamper)
    {
        $user = auth()->user();

        abort_unless($approval->approver_username === $user->username, 403);

        if ($approval->status === 'Approved') {
            return redirect()->back()->with('info', 'This sales order is already approved by you.');
        }

        if ($approval->approval_stage === 'final' && ! $this->initialApprovalsComplete($approval)) {
            return redirect()->back()->with('warning', 'CFO and President Director can approve after the first 8 approvers are complete.');
        }

        $approval->update([
            'user_id' => $user->id,
            'status' => 'Approved',
            'approved_at' => now(),
        ]);

        $salesContract = $approval->salesContract()->with('approvals')->first();
        $this->refreshSalesContractStatus($salesContract);
        $stamper->stamp($salesContract->fresh('approvals'));

        return redirect()->back()->with('success', 'Sales order approved successfully.');
    }

    private function initialApprovalsComplete(SalesContractApproval $approval): bool
    {
        return ! SalesContractApproval::where('sales_contract_id', $approval->sales_contract_id)
            ->where('approval_stage', 'initial')
            ->where('status', '!=', 'Approved')
            ->exists();
    }

    private function refreshSalesContractStatus(SalesContract $salesContract): void
    {
        $salesContract->load('approvals');

        $totalApprovals = $salesContract->approvals->count();
        $approvedCount = $salesContract->approvals->where('status', 'Approved')->count();

        if ($totalApprovals === 0) {
            return;
        }

        if ($approvedCount === $totalApprovals) {
            $salesContract->update([
                'approval_status' => 'Full Signed',
                'final_status' => 'Revision Approved',
            ]);

            return;
        }

        $initialPending = $salesContract->approvals
            ->where('approval_stage', 'initial')
            ->where('status', '!=', 'Approved')
            ->count();

        if ($initialPending === 0) {
            $salesContract->update([
                'approval_status' => 'Half Signed',
                'final_status' => $salesContract->final_status ?: 'Wait for Approval',
            ]);

            return;
        }

        if ($approvedCount > 0) {
            $salesContract->update([
                'approval_status' => 'Request Sign',
                'final_status' => $salesContract->final_status ?: 'Wait for Approval',
            ]);
        }
    }

    private function ensureApprovalRoutes(): void
    {
        $users = User::whereIn('username', SalesApprovalRoute::usernames())
            ->get()
            ->keyBy('username');

        SalesContract::query()->select('id')->chunkById(100, function ($contracts) use ($users) {
            foreach ($contracts as $contract) {
                foreach (SalesApprovalRoute::approvers() as $approver) {
                    SalesContractApproval::firstOrCreate(
                        [
                            'sales_contract_id' => $contract->id,
                            'approver_username' => $approver['username'],
                        ],
                        [
                            'user_id' => $users->get($approver['username'])?->id,
                            'approval_order' => $approver['order'],
                            'approval_stage' => $approver['stage'],
                            'approval_group' => $approver['group'],
                            'position' => $approver['position'],
                            'approver_name' => $approver['name'],
                            'status' => 'Pending',
                        ]
                    );
                }
            }
        });
    }
}
