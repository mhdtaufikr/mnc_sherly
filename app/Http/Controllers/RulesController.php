<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RulesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Rule::latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                class="btn-edit inline-flex items-center rounded-lg bg-primary px-3 py-2 text-xs font-semibold text-white transition hover:bg-primary-dark"
                                data-id="' . $row->id . '"
                                data-rule_name="' . e($row->rule_name) . '"
                                data-rule_value="' . e($row->rule_value) . '">
                                Edit
                            </button>

                            <form action="' . route('rules.destroy', $row->id) . '" method="POST" class="delete-form inline">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button
                                    type="submit"
                                    class="inline-flex items-center rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-red-700">
                                    Delete
                                </button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('rule.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'rule_name'  => 'required|string|max:255',
            'rule_value' => 'required|string|max:255',
        ]);

        Rule::create($request->only([
            'rule_name',
            'rule_value',
        ]));

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'rule_name'  => 'required|string|max:255',
            'rule_value' => 'required|string|max:255',
        ]);

        Rule::findOrFail($id)->update($request->only([
            'rule_name',
            'rule_value',
        ]));

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        Rule::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }
}
