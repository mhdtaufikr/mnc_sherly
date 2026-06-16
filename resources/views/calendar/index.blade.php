@extends('layouts.master')

@section('content')
  <div x-data="shipmentCalendarPage()" class="space-y-6">
    <div class="bg-white px-6 py-5 shadow-sm">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <div class="text-xl font-semibold text-neutral-800">Shipment Calendar</div>
          <div class="mt-1 text-sm text-slate-500">Laycan schedule generated from shipment inputs.</div>
        </div>

        <button type="button" @click="openCreate()"
          class="inline-flex items-center justify-center gap-2 rounded bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-teal-800">
          <i class="fas fa-plus text-xs"></i>
          Add Schedule
        </button>
      </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.4fr_0.6fr]">
      <div class="border-border bg-white p-4 shadow-sm">
        <div id="shipment-calendar"></div>
      </div>

      <div class="border-border bg-white p-5 shadow-sm">
        <h2 class="border-b border-slate-300 pb-2 text-lg font-semibold text-neutral-800">Status</h2>
        <div class="mt-4 space-y-3 text-sm">
          <div class="flex items-center justify-between">
            <span class="flex items-center gap-2"><span class="h-3 w-3 rounded bg-sky-700"></span>Confirmed</span>
            <span class="text-slate-500">Ready schedule</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="flex items-center gap-2"><span class="h-3 w-3 rounded bg-amber-600"></span>Loading</span>
            <span class="text-slate-500">In progress</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="flex items-center gap-2"><span class="h-3 w-3 rounded bg-green-700"></span>Complete</span>
            <span class="text-slate-500">Finished</span>
          </div>
        </div>

        <div class="mt-6 rounded bg-slate-50 p-4 text-sm text-slate-600">
          Click a calendar item to see buyer, ETA, vessel, quantity, spec, status, and discharge port.
        </div>
      </div>
    </div>

    <div class="border-border bg-white shadow-sm">
      <div class="border-b border-slate-200 px-6 py-4">
        <h2 class="text-lg font-semibold text-neutral-800">Schedule List</h2>
      </div>

      <div class="p-6">
        <div class="overflow-x-auto">
          <table id="shipment-calendar-table" class="w-full text-sm">
            <thead>
              <tr class="border-border text-text-soft border-b text-left">
                <th class="px-4 py-3">No</th>
                <th class="px-4 py-3">Buyer</th>
                <th class="px-4 py-3">No Kontrak</th>
                <th class="px-4 py-3">Laycan</th>
                <th class="px-4 py-3">ETA</th>
                <th class="px-4 py-3">TB/BG/Vessel</th>
                <th class="px-4 py-3">Qty</th>
                <th class="px-4 py-3">Spec</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Discharge Port</th>
                <th class="px-4 py-3">Action</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>

    <div x-show="open" x-transition.opacity class="fixed inset-0 z-[80] flex items-center justify-center bg-black/50 px-4 py-6"
      style="display: none;">
      <div @click.outside="closeModal()" class="flex max-h-[92vh] w-full max-w-3xl flex-col overflow-hidden rounded-lg border border-slate-200 bg-white shadow-2xl">
        <div class="flex shrink-0 items-center justify-between border-b border-slate-200 px-5 py-4">
          <div>
            <h3 class="text-lg font-semibold text-neutral-800" x-text="form.id ? 'Edit Schedule' : 'Add Schedule'"></h3>
            <p class="mt-0.5 text-xs text-slate-500">Input laycan data untuk generate kalender shipment.</p>
          </div>
          <button type="button" @click="closeModal()"
            class="inline-flex h-9 w-9 items-center justify-center rounded border border-slate-200 text-slate-700 transition hover:bg-slate-50">
            x
          </button>
        </div>

        <form @submit.prevent="submitForm" class="flex min-h-0 flex-1 flex-col">
          <div class="grid min-h-0 gap-x-4 gap-y-3 overflow-y-auto px-5 py-4 md:grid-cols-2">
            <div>
              <label class="mb-1.5 block text-sm font-medium text-slate-700">Buyer</label>
              <input type="text" x-model="form.buyer"
                class="h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-100">
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-medium text-slate-700">No Kontrak</label>
              <input type="text" x-model="form.contract_no"
                class="h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-100">
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-medium text-slate-700">Laycan Start</label>
              <input type="date" x-model="form.laycan_start"
                class="h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-100">
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-medium text-slate-700">Laycan End</label>
              <input type="date" x-model="form.laycan_end"
                class="h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-100">
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-medium text-slate-700">ETA</label>
              <input type="date" x-model="form.eta"
                class="h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-100">
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-medium text-slate-700">TB/BG/Vessel</label>
              <input type="text" x-model="form.vessel"
                class="h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-100">
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-medium text-slate-700">Qty</label>
              <input type="number" step="0.01" min="0" x-model="form.qty"
                class="h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-100">
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-medium text-slate-700">Spec</label>
              <input type="text" x-model="form.spec"
                class="h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-100">
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-medium text-slate-700">Laycan Status</label>
              <select x-model="form.laycan_status"
                class="h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-100">
                <option value="Confirmed">Confirmed</option>
                <option value="Loading">Loading</option>
                <option value="Complete">Complete</option>
              </select>
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-medium text-slate-700">Discharge Port</label>
              <input type="text" x-model="form.discharge_port"
                class="h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-100">
            </div>
          </div>

          <div class="flex shrink-0 items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4">
            <button type="button" @click="closeModal()"
              class="rounded-md border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
              Close
            </button>

            <button type="submit" :disabled="loading"
              class="rounded-md bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-teal-800 disabled:cursor-not-allowed disabled:opacity-60">
              <span x-text="loading ? 'Saving...' : 'Save'"></span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    window.calendarRoutes = {
      index: @json(route('calendar.index')),
      events: @json(route('calendar.events')),
      store: @json(route('calendar.store')),
      update: @json(route('calendar.update', ':id')),
    };

    document.addEventListener('alpine:init', () => {
      Alpine.data('shipmentCalendarPage', () => ({
        open: false,
        loading: false,
        table: null,
        calendar: null,
        form: {},

        init() {
          this.resetForm();
          this.initCalendar();
          this.initTable();
        },

        initCalendar() {
          const el = document.getElementById('shipment-calendar');
          if (!el || !window.FullCalendar) return;

          this.calendar = new window.FullCalendar.Calendar(el, {
            plugins: [
              window.FullCalendar.dayGridPlugin,
              window.FullCalendar.interactionPlugin,
              window.FullCalendar.listPlugin,
            ],
            initialView: 'dayGridMonth',
            height: 650,
            headerToolbar: {
              left: 'prev,next today',
              center: 'title',
              right: 'dayGridMonth,listMonth',
            },
            events: window.calendarRoutes.events,
            eventClick: (info) => {
              const props = info.event.extendedProps;
              const html = `
                <div class="text-left text-sm">
                  <div><b>Buyer:</b> ${props.buyer ?? '-'}</div>
                  <div><b>ETA:</b> ${props.eta ?? '-'}</div>
                  <div><b>TB/BG/Vessel:</b> ${props.vessel ?? '-'}</div>
                  <div><b>Qty:</b> ${props.qty ?? '-'}</div>
                  <div><b>Spec:</b> ${props.spec ?? '-'}</div>
                  <div><b>Status:</b> ${props.laycan_status ?? '-'}</div>
                  <div><b>Discharge Port:</b> ${props.discharge_port ?? '-'}</div>
                </div>
              `;

              window.Swal?.fire({
                title: info.event.title,
                html,
                confirmButtonText: 'Close',
              });
            },
          });

          this.calendar.render();
        },

        initTable() {
          const self = this;

          this.table = $('#shipment-calendar-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: window.calendarRoutes.index,
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
              },
              {
                data: 'buyer',
                name: 'buyer'
              },
              {
                data: 'contract_no',
                name: 'contract_no'
              },
              {
                data: 'laycan_period',
                name: 'laycan_start'
              },
              {
                data: 'eta',
                name: 'eta'
              },
              {
                data: 'vessel',
                name: 'vessel'
              },
              {
                data: 'qty',
                name: 'qty'
              },
              {
                data: 'spec',
                name: 'spec'
              },
              {
                data: 'status_badge',
                name: 'laycan_status'
              },
              {
                data: 'discharge_port',
                name: 'discharge_port'
              },
              {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
              },
            ],
          });

          $(document).on('click', '.btn-edit-calendar', function() {
            self.form = {
              id: $(this).data('id'),
              buyer: $(this).data('buyer') || '',
              contract_no: $(this).data('contract_no') || '',
              laycan_start: $(this).data('laycan_start') || '',
              laycan_end: $(this).data('laycan_end') || '',
              eta: $(this).data('eta') || '',
              vessel: $(this).data('vessel') || '',
              qty: $(this).data('qty') || '',
              spec: $(this).data('spec') || '',
              laycan_status: $(this).data('laycan_status') || 'Confirmed',
              discharge_port: $(this).data('discharge_port') || '',
            };
            self.open = true;
          });

          $('#shipment-calendar-table').on('submit', '.delete-form', async function(e) {
            e.preventDefault();

            if (!await confirmAction({
                title: 'Delete Schedule?',
                text: 'This schedule will be permanently deleted.'
              })) return;

            const form = this;

            $.ajax({
              url: form.action,
              method: 'POST',
              data: $(form).serialize(),
              success: () => {
                self.reloadData();
                window.Toast?.fire({
                  icon: 'success',
                  title: 'Schedule deleted'
                });
              },
              error: () => {
                window.Toast?.fire({
                  icon: 'error',
                  title: 'Failed to delete schedule'
                });
              }
            });
          });
        },

        resetForm() {
          this.form = {
            id: '',
            buyer: '',
            contract_no: '',
            laycan_start: '',
            laycan_end: '',
            eta: '',
            vessel: '',
            qty: '',
            spec: '',
            laycan_status: 'Confirmed',
            discharge_port: '',
          };
        },

        openCreate() {
          this.resetForm();
          this.open = true;
        },

        closeModal() {
          this.open = false;
          this.loading = false;
          this.resetForm();
        },

        reloadData() {
          this.table?.ajax.reload(null, false);
          this.calendar?.refetchEvents();
        },

        submitForm() {
          const id = this.form.id;
          const url = id ? window.calendarRoutes.update.replace(':id', id) : window.calendarRoutes.store;
          const method = id ? 'PUT' : 'POST';

          this.loading = true;

          $.ajax({
            url,
            method,
            data: {
              _token: '{{ csrf_token() }}',
              ...this.form,
            },
            success: () => {
              this.closeModal();
              this.reloadData();
              window.Toast?.fire({
                icon: 'success',
                title: 'Schedule saved successfully'
              });
            },
            error: (xhr) => {
              let message = 'Terjadi kesalahan sistem';

              if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                message = Object.values(errors).map(e => e[0]).join(', ');
              } else if (xhr.responseJSON?.message) {
                message = xhr.responseJSON.message;
              }

              window.Toast?.fire({
                icon: 'error',
                title: message
              });
            },
            complete: () => {
              this.loading = false;
            }
          });
        },
      }))
    })
  </script>
@endpush
