@extends('layouts.master')

@section('content')
  <div class="space-y-6">
    <div class="bg-primary rounded-2xl px-6 py-6 text-white shadow-sm">
      <h1 class="text-2xl font-bold">Rules</h1>
      <p class="mt-1 text-sm text-white/80">Rules Master.</p>
    </div>

    <div x-data="rulesPage()" class="border-border bg-surface rounded-2xl border shadow-sm">
      <div class="border-border flex flex-col gap-4 border-b px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 class="text-text text-lg font-semibold">List of Rules</h2>
          <p class="text-muted mt-1 text-sm">Manage rule name and rule value.</p>
        </div>

        <button type="button" @click="openCreate()"
          class="bg-primary hover:bg-primary-dark inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition">
          + Add Rule
        </button>
      </div>

      <div class="p-6">
        <div class="overflow-x-auto">
          <table id="rules-table" class="w-full text-sm">
            <thead>
              <tr class="border-border text-text-soft border-b text-left">
                <th class="px-4 py-3">No</th>
                <th class="px-4 py-3">Rule Name</th>
                <th class="px-4 py-3">Rule Value</th>
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
            <h3 class="text-text text-lg font-semibold" x-text="form.id ? 'Edit Rule' : 'Add Rule'"></h3>
            <button type="button" @click="closeModal()"
              class="border-border text-text hover:bg-surface-muted inline-flex h-10 w-10 items-center justify-center rounded-lg border transition">
              ✕
            </button>
          </div>

          <form @submit.prevent="submitForm">
            <div class="space-y-4 px-5 py-5">
              <div>
                <label class="text-text-soft mb-2 block text-sm font-medium">Rule Name</label>
                <input type="text" x-model="form.rule_name"
                  class="border-border focus:border-primary focus:ring-primary-soft w-full rounded-xl border bg-white px-4 py-3 text-sm outline-none transition focus:ring-2">
              </div>

              <div>
                <label class="text-text-soft mb-2 block text-sm font-medium">Rule Value</label>
                <input type="text" x-model="form.rule_value"
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
      rulesIndex: @json(route('rules.index')),
      rulesStore: @json(route('rules.store')),
      rulesUpdate: @json(route('rules.update', ':id')),
    };

    document.addEventListener('alpine:init', () => {
      Alpine.data('rulesPage', () => ({
        open: false,
        loading: false,
        table: null,
        form: {
          id: '',
          rule_name: '',
          rule_value: '',
        },

        init() {
          const self = this;

          this.table = $('#rules-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: window.routes.rulesIndex,
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
              },
              {
                data: 'rule_name',
                name: 'rule_name'
              },
              {
                data: 'rule_value',
                name: 'rule_value'
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
            self.form.rule_name = $(this).data('rule_name');
            self.form.rule_value = $(this).data('rule_value');
            self.open = true;
          });

          $('#rules-table').on('submit', '.delete-form', async function(e) {
            e.preventDefault();

            if (!await confirmAction({
                title: 'Delete Rule?',
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
                    title: 'Rule deleted'
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
            rule_name: '',
            rule_value: '',
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
            window.routes.rulesUpdate.replace(':id', id) :
            window.routes.rulesStore;
          const method = id ? 'PUT' : 'POST';

          this.loading = true;

          $.ajax({
            url: url,
            method: method,
            data: {
              _token: '{{ csrf_token() }}',
              rule_name: this.form.rule_name,
              rule_value: this.form.rule_value,
            },
            success: () => {
              this.closeModal();
              this.table.ajax.reload();

              if (window.Toast) {
                window.Toast.fire({
                  icon: 'success',
                  title: 'Rule saved successfully'
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
