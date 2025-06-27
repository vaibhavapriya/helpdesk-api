@extends('components.layouts.app.admin')

@section('content')
<div class="container row">
    <h2 class="d-flex justify-content-center">Queue Driver Configuration</h2>

    <div class="row d-flex justify-content-center">
        <div class="col-md-8">
            <h4>Queue Drivers</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Driver</th>
                        <th>Description</th>
                        <th>Select</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(['sync', 'database', 'redis'] as $driver)
                    <tr>
                        <td>{{ ucfirst($driver) }}</td>
                        <td>{{ ucfirst($driver) }} queue driver</td>
                        <td>
                            <div class="custom-control custom-switch">
                                <input type="radio"
                                    class="custom-control-input"
                                    id="queue_{{ $driver }}"
                                    name="queue_driver"
                                    value="{{ $driver }}"
                                    onchange="handleDriverChange(this)">
                                <label class="custom-control-label" for="queue_{{ $driver }}"></label>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="statusMessage" class="alert alert-info d-none mt-3"></div>
</div>

<!-- Optional Bootstrap CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<script>
    const token = 'Bearer ' + localStorage.getItem('auth_token');

    async function authFetch(url, options = {}) {
        return fetch(url, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                'Authorization': token,
                ...(options.headers || {})
            }
        });
    }

    document.addEventListener('DOMContentLoaded', async () => {
        try {
            const res = await authFetch('/api/admin/queue-driver');
            const data = await res.json();
            if (data.driver) {
                const el = document.querySelector(`input[name="queue_driver"][value="${data.driver}"]`);
                if (el) el.checked = true;
            }
        } catch (error) {
            console.error('Error loading queue driver:', error);
            alert('Failed to load queue driver setting.');
        }
    });

    function handleDriverChange(radio) {
        const value = radio.value;

        authFetch('/api/admin/queue-driver', {
            method: 'POST',
            body: JSON.stringify({ driver: value }),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            const msg = document.getElementById('statusMessage');
            msg.classList.remove('d-none');
            msg.textContent = `Queue driver updated to "${value}"`;
        })
        .catch(err => {
            console.error(err);
            alert('Failed to update queue driver.');
        });
    }
</script>
@endsection
