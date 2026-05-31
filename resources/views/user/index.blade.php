@extends('layouts.master')

@section('content')
  <div class="space-y-6">
    <div class="bg-primary rounded-2xl px-6 py-6 text-white shadow-sm">
      <h1 class="text-2xl font-bold">User</h1>
      <p class="mt-1 text-sm text-white/80">User Master.</p>
    </div>

    <div x-data="userPage()" class="border-border bg-surface rounded-2xl border shadow-sm">
      <div class="border-border flex flex-col gap-4 border-b px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 class="text-text text-lg font-semibold">List of User</h2>
          <p class="text-muted mt-1 text-sm">Manage user account and role.</p>
        </div>

        <button type="button" @click="openCreate()"
          class="bg-primary hover:bg-primary-dark inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition">
          + Add User
        </button>
      </div>

      <div class="p-6">
        <div class="overflow-x-auto">
          <table id="user-table" class="w-full text-sm">
            <thead>
              <tr class="border-border text-text-soft border-b text-left">
                <th class="px-4 py-3">No</th>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Username</th>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Role</th>
                <th class="px-4 py-3">Last Login</th>
                <th class="px-4 py-3">Status</th>
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
            <h3 class="text-text text-lg font-semibold" x-text="form.id ? 'Edit User' : 'Add User'"></h3>
            <button type="button" @click="closeModal()"
              class="border-border text-text hover:bg-surface-muted inline-flex h-10 w-10 items-center justify-center rounded-lg border transition">
              ✕
            </button>
          </div>

          <form @submit.prevent="submitForm">
            <div class="space-y-4 px-5 py-5">
              <div>
                <label class="text-text-soft mb-2 block text-sm font-medium">Name</label>
                <input type="text" x-model="form.name"
                  class="border-border focus:border-primary focus:ring-primary-soft w-full rounded-xl border bg-white px-4 py-3 text-sm outline-none transition focus:ring-2">
              </div>

              <div>
                <label class="text-text-soft mb-2 block text-sm font-medium">Username</label>
                <input type="text" x-model="form.username"
                  class="border-border focus:border-primary focus:ring-primary-soft w-full rounded-xl border bg-white px-4 py-3 text-sm outline-none transition focus:ring-2">
              </div>

              <div>
                <label class="text-text-soft mb-2 block text-sm font-medium">Email</label>
                <input type="email" x-model="form.email"
                  class="border-border focus:border-primary focus:ring-primary-soft w-full rounded-xl border bg-white px-4 py-3 text-sm outline-none transition focus:ring-2">
              </div>

              <div>
                <label class="text-text-soft mb-2 block text-sm font-medium">Password</label>
                <input type="password" x-model="form.password"
                  :placeholder="form.id ? 'Kosongkan jika tidak diubah' : 'Enter password'"
                  class="border-border focus:border-primary focus:ring-primary-soft w-full rounded-xl border bg-white px-4 py-3 text-sm outline-none transition focus:ring-2">
              </div>

              <div>
                <label class="text-text-soft mb-2 block text-sm font-medium">Role</label>
                <select x-model="form.role"
                  class="border-border focus:border-primary focus:ring-primary-soft w-full rounded-xl border bg-white px-4 py-3 text-sm outline-none transition focus:ring-2">
                  <option value="">Select role</option>
                  @foreach ($roles as $role)
                    <option value="{{ $role->code_format ?: $role->name_value }}">
                      {{ $role->name_value }}
                    </option>
                  @endforeach
                </select>
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
      userIndex: @json(route('user.index')),
      userStore: @json(route('user.store')),
      userUpdate: @json(route('user.update', ':id')),
      userGet: @json(route('user.get', ':id')),
      userRevoke: @json(route('user.revoke', ':id')),
      userActivate: @json(route('user.activate', ':id')),
    };

    document.addEventListener('alpine:init', () => {
      Alpine.data('userPage', () => ({
        open: false,
        loading: false,
        table: null,
        form: {
          id: '',
          name: '',
          username: '',
          email: '',
          password: '',
          role: '',
        },

        init() {
          const self = this;

          this.table = $('#user-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: window.routes.userIndex,
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
              },
              {
                data: 'name',
                name: 'name'
              },
              {
                data: 'username',
                name: 'username'
              },
              {
                data: 'email',
                name: 'email'
              },
              {
                data: 'role',
                name: 'role'
              },
              {
                data: 'last_login',
                name: 'last_login'
              },
              {
                data: 'is_active',
                name: 'is_active',
                orderable: false,
                searchable: false
              },
              {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
              },
            ],
          });

          $(document).on('click', '.btn-edit-user', function() {
            self.form.id = $(this).data('id');
            self.form.name = $(this).data('name');
            self.form.username = $(this).data('username');
            self.form.email = $(this).data('email');
            self.form.password = '';
            self.form.role = $(this).data('role');
            self.open = true;
          });

          $(document).on('click', '.btn-revoke-user', async function() {
            const id = $(this).data('id');

            if (!await confirmAction({
                title: 'Revoke User?',
                text: 'User will be set as inactive.'
              })) return;

            $.ajax({
              url: window.routes.userRevoke.replace(':id', id),
              method: 'POST',
              data: {
                _token: '{{ csrf_token() }}'
              },
              success: () => {
                self.table.ajax.reload();
                window.Toast?.fire({
                  icon: 'success',
                  title: 'User revoked successfully'
                });
              },
              error: () => {
                window.Toast?.fire({
                  icon: 'error',
                  title: 'Failed to revoke user'
                });
              }
            });
          });

          $(document).on('click', '.btn-activate-user', async function() {
            const id = $(this).data('id');

            if (!await confirmAction({
                title: 'Activate User?',
                text: 'User will be activated again.'
              })) return;

            $.ajax({
              url: window.routes.userActivate.replace(':id', id),
              method: 'POST',
              data: {
                _token: '{{ csrf_token() }}'
              },
              success: () => {
                self.table.ajax.reload();
                window.Toast?.fire({
                  icon: 'success',
                  title: 'User activated successfully'
                });
              },
              error: () => {
                window.Toast?.fire({
                  icon: 'error',
                  title: 'Failed to activate user'
                });
              }
            });
          });
        },

        resetForm() {
          this.form = {
            id: '',
            name: '',
            username: '',
            email: '',
            password: '',
            role: '',
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
            window.routes.userUpdate.replace(':id', id) :
            window.routes.userStore;
          const method = id ? 'PATCH' : 'POST';

          this.loading = true;

          $.ajax({
            url: url,
            method: method,
            data: {
              _token: '{{ csrf_token() }}',
              name: this.form.name,
              username: this.form.username,
              email: this.form.email,
              password: this.form.password,
              role: this.form.role,
            },
            success: () => {
              this.closeModal();
              this.table.ajax.reload();
              window.Toast?.fire({
                icon: 'success',
                title: 'User saved successfully'
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
        }
      }))
    })
  </script>
@endpush
