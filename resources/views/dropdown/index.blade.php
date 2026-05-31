@extends('layouts.master')

@section('content')
  <div class="space-y-6">
    <div class="bg-primary rounded-2xl px-6 py-6 text-white shadow-sm">
      <h1 class="text-2xl font-bold">Dropdown</h1>
      <p class="mt-1 text-sm text-white/80">Dropdown Master.</p>
    </div>

    <div x-data="dropdownPage()" class="border-border bg-surface rounded-2xl border shadow-sm">
      <div class="border-border flex flex-col gap-4 border-b px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 class="text-text text-lg font-semibold">List of Dropdown</h2>
          <p class="text-muted mt-1 text-sm">Manage dropdown category, value, and code format.</p>
        </div>

        <button type="button" @click="openCreate()"
          class="bg-primary hover:bg-primary-dark inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition">
          + Add Dropdown
        </button>
      </div>

      <div class="p-6">
        <div class="overflow-x-auto">
          <table id="dropdown-table" class="w-full text-sm">
            <thead>
              <tr class="border-border text-text-soft border-b text-left">
                <th class="px-4 py-3">No</th>
                <th class="px-4 py-3">Category</th>
                <th class="px-4 py-3">Name Value</th>
                <th class="px-4 py-3">Code Format</th>
                <th class="px-4 py-3">Action</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>

      <div x-show="open" x-transition.opacity
        class="fixed inset-0 z-[80] flex items-center justify-center bg-black/50 px-4" style="display: none;">
        <div @click.outside="closeModal()" class="border-border bg-surface w-full max-w-lg rounded-2xl border shadow-2xl">
          <div class="border-border flex items-center justify-between border-b px-5 py-4">
            <h3 class="text-text text-lg font-semibold" x-text="form.id ? 'Edit Dropdown' : 'Add Dropdown'"></h3>
            <button type="button" @click="closeModal()"
              class="border-border text-text hover:bg-surface-muted inline-flex h-10 w-10 items-center justify-center rounded-lg border transition">
              ✕
            </button>
          </div>

          <form @submit.prevent="submitForm">
            <div class="space-y-4 px-5 py-5">
              <div>
                <label class="text-text-soft mb-2 block text-sm font-medium">Category</label>
                <input type="text" x-model="form.category"
                  class="border-border focus:border-primary focus:ring-primary-soft w-full rounded-xl border bg-white px-4 py-3 text-sm outline-none transition focus:ring-2">
              </div>

              <div>
                <label class="text-text-soft mb-2 block text-sm font-medium">Name Value</label>
                <input type="text" x-model="form.name_value"
                  class="border-border focus:border-primary focus:ring-primary-soft w-full rounded-xl border bg-white px-4 py-3 text-sm outline-none transition focus:ring-2">
              </div>

              <div>
                <label class="text-text-soft mb-2 block text-sm font-medium">Code Format</label>
                <input type="text" x-model="form.code_format"
                  class="border-border focus:border-primary focus:ring-primary-soft w-full rounded-xl border bg-white px-4 py-3 text-sm outline-none transition focus:ring-2">
              </div>
            </div>

            <div class="border-border flex items-center justify-end gap-3 border-t px-5 py-4">
              <button type="button" @click="closeModal()"
                class="border-border text-text hover:bg-surface-muted rounded-xl border px-4 py-2.5 text-sm font-medium transition">
                Close
              </button>

              <button type="submit" :disabled="loading"
                class="bg-primary hover:bg-primary-dark rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition disabled:cursor-not-allowed disabled:opacity-60">
                <span x-text="loading ? 'Saving...' : 'Save'"></span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    window.routes = {
      dropdownIndex: @json(route('dropdown.index')),
      dropdownStore: @json(route('dropdown.store')),
      dropdownUpdate: @json(route('dropdown.update', ':id')),
    };

    document.addEventListener('alpine:init', () => {
      Alpine.data('dropdownPage', () => ({
        open: false,
        loading: false,
        table: null,
        form: {
          id: '',
          category: '',
          name_value: '',
          code_format: '',
        },

        init() {
          const self = this;

          this.table = $('#dropdown-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: window.routes.dropdownIndex,
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
              },
              {
                data: 'category',
                name: 'category'
              },
              {
                data: 'name_value',
                name: 'name_value'
              },
              {
                data: 'code_format',
                name: 'code_format'
              },
              {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
              },
            ],
          });

          $(document).on('click', '.btn-edit', function() {
            self.form.id = $(this).data('id');
            self.form.category = $(this).data('category');
            self.form.name_value = $(this).data('name_value');
            self.form.code_format = $(this).data('code_format');
            self.open = true;
          });

          $('#dropdown-table').on('submit', '.delete-form', async function(e) {
            e.preventDefault();

            if (!await confirmAction({
                title: 'Delete Dropdown?',
                text: 'This data will be permanently deleted.'
              })) return;

            const form = this;

            $.ajax({
              url: form.action,
              method: 'POST',
              data: $(form).serialize(),
              success: () => {
                self.table.ajax.reload();

                if (window.Toast) {
                  window.Toast.fire({
                    icon: 'success',
                    title: 'Dropdown deleted'
                  });
                }
              },
              error: () => {
                if (window.Toast) {
                  window.Toast.fire({
                    icon: 'error',
                    title: 'Failed to delete data'
                  });
                }
              }
            });
          });
        },

        resetForm() {
          this.form = {
            id: '',
            category: '',
            name_value: '',
            code_format: '',
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

        submitForm() {
          const id = this.form.id;
          const url = id ?
            window.routes.dropdownUpdate.replace(':id', id) :
            window.routes.dropdownStore;
          const method = id ? 'PUT' : 'POST';

          this.loading = true;

          $.ajax({
            url: url,
            method: method,
            data: {
              _token: '{{ csrf_token() }}',
              category: this.form.category,
              name_value: this.form.name_value,
              code_format: this.form.code_format,
            },
            success: () => {
              this.closeModal();
              this.table.ajax.reload();

              if (window.Toast) {
                window.Toast.fire({
                  icon: 'success',
                  title: 'Dropdown saved successfully'
                });
              }
            },
            error: (xhr) => {
              let message = 'Terjadi kesalahan sistem';

              if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                message = Object.values(errors).map(e => e[0]).join(', ');
              } else if (xhr.responseJSON?.message) {
                message = xhr.responseJSON.message;
              }

              if (window.Toast) {
                window.Toast.fire({
                  icon: 'error',
                  title: message
                });
              }
            },
            complete: () => {
              this.loading = false;
            }
          });
        }
      }))
    })
  </script>
@endpush
