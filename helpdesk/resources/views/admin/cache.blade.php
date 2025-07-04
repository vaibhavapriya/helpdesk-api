@extends('components.layouts.app.admin')

@section('content')
<div class="container">
    <h2 class="d-flex justify-content-center">Cache Driver Configuration</h2>

    <div class="row d-flex justify-content-center">
        <div class="col-md-8">
            <h4>Cache Drivers</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Driver</th>
                        <th>Description</th>
                        <th>Select</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(['file', 'redis', 'array', 'database'] as $driver)
                    <tr>
                        <td>{{ ucfirst($driver) }}</td>
                        <td>{{ ucfirst($driver) }} cache driver</td>
                        <td>
                            <div class="custom-control custom-switch">
                                <input type="radio"
                                    class="custom-control-input"
                                    id="cache_{{ $driver }}"
                                    name="cache_driver"
                                    value="{{ $driver }}"
                                    onchange="handleDriverChange(this)">
                                <label class="custom-control-label" for="cache_{{ $driver }}"></label>
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
            const res = await authFetch('/api/admin/cache-driver');
            const data = await res.json();
            if (data.driver) {
                const el = document.querySelector(`input[name="cache_driver"][value="${data.driver}"]`);
                if (el) el.checked = true;
            }
        } catch (error) {
            console.error('Error loading cache driver:', error);
            alert('Failed to load cache driver setting.');
        }
    });

    function handleDriverChange(radio) {
        const value = radio.value;

        authFetch('/api/admin/cache-driver', {
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
            msg.textContent = `Cache driver updated to "${value}"`;
        })
        .catch(err => {
            console.error(err);
            alert('Failed to update cache driver.');
        });
    }
</script>
@endsection


