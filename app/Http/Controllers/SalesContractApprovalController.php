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

        $stamper->stamp($approval->salesContract()->with('approvals')->first());

        return redirect()->back()->with('success', 'Sales order approved successfully.');
    }

    private function initialApprovalsComplete(SalesContractApproval $approval): bool
    {
        return ! SalesContractApproval::where('sales_contract_id', $approval->sales_contract_id)
            ->where('approval_stage', 'initial')
            ->where('status', '!=', 'Approved')
            ->exists();
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
