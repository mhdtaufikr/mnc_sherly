<?php

namespace App\Http\Controllers;

use App\Models\Dropdown;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DropdownsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Dropdown::latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                class="btn-edit inline-flex items-center rounded-lg bg-primary px-3 py-2 text-xs font-semibold text-white transition hover:bg-primary-dark"
                                data-id="' . $row->id . '"
                                data-category="' . e($row->category) . '"
                                data-name_value="' . e($row->name_value) . '"
                                data-code_format="' . e($row->code_format) . '">
                                Edit
                            </button>

                            <form action="' . route('dropdown.destroy', $row->id) . '" method="POST" class="delete-form inline">
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

        return view('dropdown.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category'    => 'required|string|max:255',
            'name_value'  => 'required|string|max:255',
            'code_format' => 'required|string|max:255',
        ]);

        Dropdown::create($request->only([
            'category',
            'name_value',
            'code_format',
        ]));

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category'    => 'required|string|max:255',
            'name_value'  => 'required|string|max:255',
            'code_format' => 'required|string|max:255',
        ]);

        Dropdown::findOrFail($id)->update($request->only([
            'category',
            'name_value',
            'code_format',
        ]));

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        Dropdown::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }
}