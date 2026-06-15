@extends('layouts.master')

@php
  $salesContract ??= null;
  $isEdit = $salesContract !== null;
  $fieldValue = function ($field, $default = null) use ($salesContract) {
      $value = $salesContract?->{$field} ?? $default;

      if ($value instanceof DateTimeInterface) {
          $value = $value->format('Y-m-d');
      }

      return old($field, $value);
  };

  $fieldClass = 'h-8 w-full border border-slate-300 bg-white px-2 text-sm text-slate-800 outline-none transition focus:border-teal-700 focus:ring-1 focus:ring-teal-700';
  $readonlyClass = 'h-8 w-full border border-slate-300 bg-slate-100 px-2 text-sm font-semibold text-slate-600 outline-none';
  $labelClass = 'flex items-center gap-2 text-sm text-slate-700 after:h-px after:flex-1 after:border-t after:border-dotted after:border-slate-300';

  $sellerEntities = ['PMC', 'IBPE', 'APE'];
  $marketTypes = ['Domestic', 'Export'];
  $draftStatuses = ['Draft', 'Under Review', 'Pending Approval', 'Confirmed', 'Cancelled'];
  $commodities = ['Cooking Indonesian Origin', 'Non Cooking Indonesian Origin'];
  $incoterms = ['FOB', 'CIF'];
  $garGcvs = ['2700', '2800', '3000', '3500'];
  $sizes = ['No Sizing', 'Sizing'];
  $priceTypes = ['Fixed Price', 'Formula'];
  $barges = ['FOB Barge', 'FOB MV GNG', 'FOB MV Gearless'];
  $dmoStatuses = ['DMO', 'Non DMO'];
  $laycanStatuses = ['Confirm', 'Nego Laycan'];
  $approvalStatuses = ['Request Sign', 'Half Signed', 'Full Signed'];
  $finalStatuses = ['Wait for Approval', 'On Hold', 'Revision Approved'];
  $approvalRoutes = [
      ['group' => 'Marketing & Sales Operation', 'role' => 'Marketing Head', 'name' => 'Hardianti Asmi'],
      ['group' => 'Marketing & Sales Operation', 'role' => 'Sales Operation Head', 'name' => 'Hengki Dwiyanto'],
      ['group' => 'Marketing & Sales Operation', 'role' => 'General Manager Marketing & Sales Operation', 'name' => 'Daniel Tambunan'],
      ['group' => 'Finance & Accounting', 'role' => 'Finance Head', 'name' => 'Dina Anggraini'],
      ['group' => 'Finance & Accounting', 'role' => 'General Manager Finance', 'name' => 'Mochamad Ari'],
      ['group' => 'Finance & Accounting', 'role' => 'General Manager Accounting', 'name' => 'Chandra Liew'],
      ['group' => 'Legal', 'role' => 'General Manager Legal', 'name' => 'Gillbert T'],
      ['group' => 'Board Approval', 'role' => 'Deputy CFO', 'name' => 'Christian'],
      ['group' => 'Board Approval', 'role' => 'CFO', 'name' => 'Andrea Frans Tambunan'],
      ['group' => 'Board Approval', 'role' => 'President Director', 'name' => 'Suryo Eko Hardianto'],
  ];
@endphp

@section('content')
  <div x-data="salesContractForm()" class="space-y-4">
    <form method="POST" action="{{ $isEdit ? route('sales-contracts.update', $salesContract) : route('sales-contracts.store') }}" enctype="multipart/form-data">
      @csrf
      @if ($isEdit)
        @method('PUT')
      @endif

      <div class="bg-white shadow-sm">
        <div class="flex items-center gap-3 border-b border-slate-200 px-5 py-3">
          <a href="{{ route('sales-contracts.index') }}"
            class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-teal-50 text-teal-800 transition hover:bg-teal-100"
            aria-label="Back">
            <i class="fas fa-arrow-left"></i>
          </a>
          <div class="min-w-0">
            <h1 class="text-2xl font-semibold text-slate-800">{{ $isEdit ? 'Edit Sales Order' : 'Sales Order' }}</h1>
            <p class="mt-0.5 text-sm text-slate-500">Contract entry form for marketing, shipment, quality, pricing, and approval.</p>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-6 border-b border-slate-200 px-5 py-2 text-sm text-slate-700">
          <button type="submit" class="inline-flex items-center gap-2 text-teal-800 hover:text-teal-950">
            <i class="fas fa-floppy-disk"></i>
            {{ $isEdit ? 'Update' : 'Save' }}
          </button>
          <button type="reset" class="inline-flex items-center gap-2 hover:text-slate-950">
            <i class="fas fa-rotate-left"></i>
            Clear
          </button>
          <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 hover:text-slate-950">
            <i class="fas fa-print"></i>
            Print
          </button>
          <span class="h-5 border-l border-slate-300"></span>
          <span>Process</span>
          <span>Release</span>
          <span>Posting</span>
          <span>Request Approval</span>
          <span class="text-slate-400">More options</span>
        </div>
      </div>

      <div class="grid gap-5 xl:grid-cols-[1fr_320px]">
        <div class="space-y-4 bg-white px-5 py-4 shadow-sm">
          <section>
            <div class="mb-3 flex items-center justify-between border-b-2 border-slate-500 pb-1">
              <h2 class="text-base font-bold text-slate-800">Basic Sales Information</h2>
              <span class="text-xs text-slate-500">Show more</span>
            </div>
            <div class="grid gap-x-8 gap-y-2 lg:grid-cols-2">
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Contract Number</label>
                <input name="contract_number" value="{{ $fieldValue('contract_number') }}" class="{{ $fieldClass }}" placeholder="Auto generated if empty">
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Buyer Name</label>
                <input name="buyer_name" value="{{ $fieldValue('buyer_name') }}" list="buyer-suggestions" class="{{ $fieldClass }}">
                <datalist id="buyer-suggestions">
                  @foreach ($buyers as $buyer)
                    <option value="{{ $buyer }}"></option>
                  @endforeach
                </datalist>
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Buyer Reference</label>
                <input name="buyer_reference" value="{{ $fieldValue('buyer_reference') }}" class="{{ $fieldClass }}">
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Entity Penjual</label>
                <select name="seller_entity" class="{{ $fieldClass }}">
                  <option value="">Select</option>
                  @foreach ($sellerEntities as $option)
                    <option value="{{ $option }}" @selected($fieldValue('seller_entity') === $option)>{{ $option }}</option>
                  @endforeach
                </select>
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Market Type</label>
                <select name="market_type" x-model="marketType" class="{{ $fieldClass }}">
                  <option value="">Select</option>
                  @foreach ($marketTypes as $option)
                    <option value="{{ $option }}" @selected($fieldValue('market_type', 'Domestic') === $option)>{{ $option }}</option>
                  @endforeach
                </select>
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">PIC Marketing</label>
                <input name="pic_marketing" value="{{ $fieldValue('pic_marketing') }}" class="{{ $fieldClass }}">
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Submission Date</label>
                <input type="date" name="submission_date" value="{{ $fieldValue('submission_date') }}" class="{{ $fieldClass }}">
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Submitted By</label>
                <input name="submitted_by" value="{{ $fieldValue('submitted_by', auth()->user()->name) }}" class="{{ $fieldClass }}">
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Status Draft</label>
                <select name="draft_status" class="{{ $fieldClass }}">
                  <option value="">Select</option>
                  @foreach ($draftStatuses as $option)
                    <option value="{{ $option }}" @selected($fieldValue('draft_status', 'Draft') === $option)>{{ $option }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </section>

          <section>
            <div class="mb-3 border-b-2 border-slate-500 pb-1">
              <h2 class="text-base font-bold text-slate-800">Contract Summary</h2>
            </div>
            <div class="grid gap-x-8 gap-y-2 lg:grid-cols-2">
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Commodity</label>
                <select name="commodity" class="{{ $fieldClass }}">
                  <option value="">Select</option>
                  @foreach ($commodities as $option)
                    <option value="{{ $option }}" @selected($fieldValue('commodity') === $option)>{{ $option }}</option>
                  @endforeach
                </select>
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Contract Quantity</label>
                <div class="flex">
                  <input type="number" step="0.01" min="0" name="contract_quantity_mt" value="{{ $fieldValue('contract_quantity_mt') }}" class="{{ $fieldClass }}">
                  <span class="inline-flex h-8 items-center border border-l-0 border-slate-300 bg-slate-100 px-3 text-xs font-semibold text-slate-600">MT</span>
                </div>
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Sales Quantity</label>
                <div class="flex">
                  <input type="number" step="0.01" min="0" name="sales_quantity_mt" value="{{ $fieldValue('sales_quantity_mt') }}" class="{{ $fieldClass }}">
                  <span class="inline-flex h-8 items-center border border-l-0 border-slate-300 bg-slate-100 px-3 text-xs font-semibold text-slate-600">MT</span>
                </div>
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Shipment Period</label>
                <input type="text" name="shipment_period" value="{{ $fieldValue('shipment_period') }}" class="{{ $fieldClass }}" placeholder="May 2026">
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Incoterms</label>
                <select name="incoterms" class="{{ $fieldClass }}">
                  <option value="">Select</option>
                  @foreach ($incoterms as $option)
                    <option value="{{ $option }}" @selected($fieldValue('incoterms') === $option)>{{ $option }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </section>

          <section>
            <div class="mb-3 border-b-2 border-slate-500 pb-1">
              <h2 class="text-base font-bold text-slate-800">Quality Specification</h2>
            </div>
            <div class="grid gap-x-8 gap-y-2 lg:grid-cols-2">
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">GAR / GCV</label>
                <select name="gar_gcv" class="{{ $fieldClass }}">
                  <option value="">Select</option>
                  @foreach ($garGcvs as $option)
                    <option value="{{ $option }}" @selected($fieldValue('gar_gcv') === $option)>{{ $option }}</option>
                  @endforeach
                </select>
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Actual GAR</label>
                <input name="actual_gar" value="{{ $fieldValue('actual_gar') }}" class="{{ $fieldClass }}">
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Total Moisture</label>
                <input name="total_moisture" value="{{ $fieldValue('total_moisture') }}" class="{{ $fieldClass }}">
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Inherent Moisture</label>
                <input name="inherent_moisture" value="{{ $fieldValue('inherent_moisture') }}" class="{{ $fieldClass }}">
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Ash</label>
                <div class="grid grid-cols-[1fr_70px_1fr]">
                  <input name="ash" value="{{ $fieldValue('ash') }}" class="{{ $fieldClass }}">
                  <span class="inline-flex h-8 items-center justify-center border-y border-slate-300 bg-slate-100 text-xs font-semibold text-slate-600">%</span>
                  <input name="ash_limit" value="{{ $fieldValue('ash_limit', '>10%') }}" class="{{ $fieldClass }}">
                </div>
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Sulphur</label>
                <div class="grid grid-cols-[1fr_70px_1fr]">
                  <input name="sulphur" value="{{ $fieldValue('sulphur') }}" class="{{ $fieldClass }}">
                  <span class="inline-flex h-8 items-center justify-center border-y border-slate-300 bg-slate-100 text-xs font-semibold text-slate-600">%</span>
                  <input name="sulphur_limit" value="{{ $fieldValue('sulphur_limit', '>0,6%') }}" class="{{ $fieldClass }}">
                </div>
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Size</label>
                <select name="size" class="{{ $fieldClass }}">
                  <option value="">Select</option>
                  @foreach ($sizes as $option)
                    <option value="{{ $option }}" @selected($fieldValue('size') === $option)>{{ $option }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </section>

          <section>
            <div class="mb-3 border-b-2 border-slate-500 pb-1">
              <h2 class="text-base font-bold text-slate-800">Pricing and Commercial Terms</h2>
            </div>
            <div class="grid gap-x-8 gap-y-2 lg:grid-cols-2">
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Pricing Basis</label>
                <input name="pricing_basis" value="ICI" class="{{ $readonlyClass }}" readonly>
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Price Type</label>
                <select name="price_type" x-model="priceType" class="{{ $fieldClass }}">
                  <option value="">Select</option>
                  @foreach ($priceTypes as $option)
                    <option value="{{ $option }}" @selected($fieldValue('price_type') === $option)>{{ $option }}</option>
                  @endforeach
                </select>
              </div>
              <div x-show="priceType === 'Fixed Price'" class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Fixed Price</label>
                <div class="flex">
                  <input type="number" step="0.01" min="0" name="fixed_price" value="{{ $fieldValue('fixed_price') }}" class="{{ $fieldClass }}">
                  <span class="inline-flex h-8 min-w-14 items-center justify-center border border-l-0 border-slate-300 bg-slate-100 px-3 text-xs font-semibold text-slate-600" x-text="currency"></span>
                </div>
              </div>
              <div x-show="priceType === 'Formula'" class="grid items-start gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }} pt-1.5">Formula</label>
                <textarea name="formula_price" rows="3" class="w-full border border-slate-300 bg-white px-2 py-1.5 text-sm text-slate-800 outline-none transition focus:border-teal-700 focus:ring-1 focus:ring-teal-700">{{ $fieldValue('formula_price') }}</textarea>
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Minus / Plus</label>
                <div class="flex">
                  <span class="inline-flex h-8 items-center border border-r-0 border-slate-300 bg-slate-100 px-3 text-xs font-semibold text-slate-600">$</span>
                  <input type="number" step="0.01" name="minus_plus" value="{{ $fieldValue('minus_plus') }}" class="{{ $fieldClass }}">
                </div>
              </div>
              <div class="grid items-start gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }} pt-1.5">Payment Term Summary</label>
                <textarea name="payment_term_summary" rows="3" placeholder="50% TT after, 50% LC" class="w-full border border-slate-300 bg-white px-2 py-1.5 text-sm text-slate-800 outline-none transition focus:border-teal-700 focus:ring-1 focus:ring-teal-700">{{ $fieldValue('payment_term_summary') }}</textarea>
              </div>
            </div>
          </section>

          <section>
            <div class="mb-3 border-b-2 border-slate-500 pb-1">
              <h2 class="text-base font-bold text-slate-800">Shipping and Laycan Information</h2>
            </div>
            <div class="grid gap-x-8 gap-y-2 lg:grid-cols-2">
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Shipment No</label>
                <input name="shipment_no" value="{{ $fieldValue('shipment_no') }}" class="{{ $fieldClass }}">
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Barges</label>
                <select name="barges" class="{{ $fieldClass }}">
                  <option value="">Select</option>
                  @foreach ($barges as $option)
                    <option value="{{ $option }}" @selected($fieldValue('barges') === $option)>{{ $option }}</option>
                  @endforeach
                </select>
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">ETA</label>
                <input type="date" name="eta" value="{{ $fieldValue('eta') }}" class="{{ $fieldClass }}">
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Laycan Start Date</label>
                <input type="date" name="laycan_start_date" value="{{ $fieldValue('laycan_start_date') }}" class="{{ $fieldClass }}">
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr] lg:col-span-2">
                <label class="{{ $labelClass }}">Laycan End Date</label>
                <input type="date" name="laycan_end_date" value="{{ $fieldValue('laycan_end_date') }}" class="{{ $fieldClass }} max-w-md">
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Load Port</label>
                <input name="load_port" value="{{ $fieldValue('load_port') }}" class="{{ $fieldClass }}">
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Destination Port</label>
                <input name="destination_port" value="{{ $fieldValue('destination_port') }}" class="{{ $fieldClass }}">
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Tug Boat Name</label>
                <input name="tug_boat_name" value="{{ $fieldValue('tug_boat_name') }}" class="{{ $fieldClass }}">
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Barge / Vessel Name</label>
                <input name="barge_vessel_name" value="{{ $fieldValue('barge_vessel_name') }}" class="{{ $fieldClass }}">
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Barge / Vessel Agent</label>
                <input name="barge_vessel_agent" value="{{ $fieldValue('barge_vessel_agent') }}" class="{{ $fieldClass }}">
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">DMO / Non DMO</label>
                <select name="dmo_status" class="{{ $fieldClass }}">
                  <option value="">Select</option>
                  @foreach ($dmoStatuses as $option)
                    <option value="{{ $option }}" @selected($fieldValue('dmo_status') === $option)>{{ $option }}</option>
                  @endforeach
                </select>
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Surveyor</label>
                <input name="surveyor" value="{{ $fieldValue('surveyor') }}" class="{{ $fieldClass }}">
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Laycan Status</label>
                <select name="laycan_status" class="{{ $fieldClass }}">
                  <option value="">Select</option>
                  @foreach ($laycanStatuses as $option)
                    <option value="{{ $option }}" @selected($fieldValue('laycan_status') === $option)>{{ $option }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </section>

          <section>
            <div class="mb-3 border-b-2 border-slate-500 pb-1">
              <h2 class="text-base font-bold text-slate-800">Approval and Tracking</h2>
            </div>
            <div class="grid gap-x-8 gap-y-2 lg:grid-cols-2">
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Approval Status</label>
                <select name="approval_status" class="{{ $fieldClass }}">
                  <option value="">Select</option>
                  @foreach ($approvalStatuses as $option)
                    <option value="{{ $option }}" @selected($fieldValue('approval_status') === $option)>{{ $option }}</option>
                  @endforeach
                </select>
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Approval Date</label>
                <input type="date" name="approval_date" value="{{ $fieldValue('approval_date') }}" class="{{ $fieldClass }}">
              </div>
              <div class="grid items-center gap-2 sm:grid-cols-[150px_1fr]">
                <label class="{{ $labelClass }}">Final Status</label>
                <select name="final_status" class="{{ $fieldClass }}">
                  <option value="">Select</option>
                  @foreach ($finalStatuses as $option)
                    <option value="{{ $option }}" @selected($fieldValue('final_status') === $option)>{{ $option }}</option>
                  @endforeach
                </select>
              </div>
              <div class="grid items-start gap-2 sm:grid-cols-[150px_1fr] lg:col-span-2">
                <label class="{{ $labelClass }} pt-1.5">Revision Note</label>
                <textarea name="revision_note" rows="3" class="w-full border border-slate-300 bg-white px-2 py-1.5 text-sm text-slate-800 outline-none transition focus:border-teal-700 focus:ring-1 focus:ring-teal-700">{{ $fieldValue('revision_note') }}</textarea>
              </div>
              <div class="lg:col-span-2">
                <div class="mt-3 border border-slate-200 bg-slate-50 p-4">
                  <div class="mb-3 flex items-center justify-between border-b border-slate-300 pb-2">
                    <h3 class="text-sm font-bold text-slate-800">Approval Route</h3>
                    <span class="text-xs font-semibold text-teal-700">Fixed routing</span>
                  </div>

                  <div class="grid gap-3 md:grid-cols-2">
                    @foreach ($approvalRoutes as $route)
                      <div class="grid grid-cols-[32px_1fr] gap-3 border border-slate-200 bg-white p-3">
                        <div class="flex h-8 w-8 items-center justify-center bg-teal-700 text-xs font-bold text-white">
                          {{ $loop->iteration }}
                        </div>
                        <div class="min-w-0">
                          <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $route['group'] }}</div>
                          <div class="mt-1 text-sm font-semibold text-slate-800">{{ $route['role'] }}</div>
                          <div class="text-sm text-slate-600">{{ $route['name'] }}</div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>

        <aside class="space-y-4">
          <div class="bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-center gap-5 border-b border-slate-300">
              <button type="button" class="border-b-2 border-teal-700 px-1 pb-2 text-sm font-semibold text-slate-800">
                <i class="fas fa-circle-info mr-1 text-slate-500"></i>
                Details
              </button>
              <button type="button" class="px-1 pb-2 text-sm font-semibold text-slate-500">
                <i class="fas fa-paperclip mr-1"></i>
                Attachments
              </button>
            </div>

            <h3 class="text-base font-bold text-slate-800">Upload Contract</h3>
            @if ($isEdit && $salesContract->contract_file_path)
              <a href="{{ asset('storage/' . $salesContract->contract_file_path) }}" target="_blank"
                class="mt-3 inline-flex text-sm font-semibold text-teal-700 hover:text-teal-900 hover:underline">
                Current file: {{ $salesContract->contract_file_name ?? 'Open contract file' }}
              </a>
            @endif
            <label class="mt-4 flex cursor-pointer flex-col items-center justify-center border-2 border-dashed border-teal-200 bg-teal-50/60 px-4 py-8 text-center transition hover:bg-teal-50">
              <i class="fas fa-file-arrow-up text-3xl text-teal-700"></i>
              <span class="mt-3 text-sm font-semibold text-slate-800" x-text="fileName || 'Choose contract file'"></span>
              <span class="mt-1 text-xs text-slate-500">PDF, Word, Excel, JPG, or PNG up to 10 MB</span>
              <input type="file" name="contract_file" class="sr-only" @change="fileName = $event.target.files[0]?.name || ''">
            </label>
          </div>

          <div class="bg-white p-5 shadow-sm">
            <h3 class="border-b border-slate-300 pb-2 text-base font-bold text-slate-800">Sales Contract History</h3>
            <div class="mt-4 grid grid-cols-2 gap-2">
              @foreach (['Draft', 'Review', 'Request Sign', 'Half Signed', 'Full Signed', 'Revision'] as $label)
                <div class="bg-teal-700 p-3 text-white">
                  <div class="text-2xl font-light">0</div>
                  <div class="mt-3 text-xs leading-tight">{{ $label }}</div>
                </div>
              @endforeach
            </div>
          </div>

          <div class="bg-white p-5 shadow-sm">
            <h3 class="border-b border-slate-300 pb-2 text-base font-bold text-slate-800">Recent Contracts</h3>
            <div class="mt-3 space-y-3">
              @forelse ($recentContracts as $contract)
                <div class="border-b border-slate-100 pb-3 text-sm last:border-b-0 last:pb-0">
                  <div class="font-semibold text-slate-800">{{ $contract->contract_number }}</div>
                  <div class="text-slate-500">{{ $contract->buyer_name }}</div>
                  <div class="mt-1 text-xs text-teal-700">{{ $contract->final_status ?? $contract->draft_status }}</div>
                </div>
              @empty
                <p class="text-sm text-slate-500">No contract saved yet.</p>
              @endforelse
            </div>
          </div>
        </aside>
      </div>
    </form>
  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('salesContractForm', () => ({
        marketType: @json($fieldValue('market_type', 'Domestic')),
        priceType: @json($fieldValue('price_type', 'Fixed Price')),
        fileName: '',
        get currency() {
          return this.marketType === 'Export' ? 'USD' : 'IDR';
        },
      }));
    });
  </script>
@endpush

