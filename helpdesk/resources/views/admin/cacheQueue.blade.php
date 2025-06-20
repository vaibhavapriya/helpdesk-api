@extends('components.layouts.app.admin')

@section('content')
<div class="container">
    <h2>Queue & Cache Driver Configuration</h2>

    <div class="row">
        <!-- Queue Driver Table -->
        <div class="col-md-6">
            <h4>Queue Drivers</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Driver</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- beanstalkd -->
                    @foreach(['sync', 'database', 'redis', 'null'] as $driver)
                    <tr>
                        <td>
                            <input type="radio" name="queue_driver" value="{{ $driver }}" onchange="handleDriverChange(this)">
                        </td>
                        <td>{{ ucfirst($driver) }}</td>
                        <td>{{ ucfirst($driver) }} queue driver</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Cache Driver Table -->
        <div class="col-md-6">
            <h4>Cache Drivers</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Driver</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- memcache -->
                    @foreach(['file', 'redis', 'array', 'database'] as $driver)
                    <tr>
                        <td>
                            <input type="radio" name="cache_driver" value="{{ $driver }}" onchange="handleDriverChange(this)">
                        </td>
                        <td>{{ ucfirst($driver) }}</td>
                        <td>{{ ucfirst($driver) }} cache driver</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="statusMessage" class="alert alert-info d-none mt-3"></div>
</div>

<script>
    const token = 'Bearer ' + localStorage.getItem('auth_token');

    // Helper to fetch with auth and JSON
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

    // Fetch current queue & cache drivers and mark them as selected
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            const [queueRes, cacheRes] = await Promise.all([
                authFetch('/api/admin/queue-driver'),
                authFetch('/api/admin/cache-driver')
            ]);

            const queue = await queueRes.json();
            const cache = await cacheRes.json();

            if (queue.driver) {
                document.querySelector(`input[name="queue_driver"][value="${queue.driver}"]`)?.setAttribute('checked', true);
            }

            if (cache.driver) {
                document.querySelector(`input[name="cache_driver"][value="${cache.driver}"]`)?.setAttribute('checked', true);
            }
        } catch (error) {
            console.error('Error loading drivers:', error);
            alert('Failed to load current driver settings.');
        }
    });

    function handleDriverChange(radio) {
        const type = radio.name === 'queue_driver' ? 'queue' : 'cache';
        const endpoint = `/api/admin/${type}-driver`;
        const value = radio.value;

        authFetch(endpoint, {
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
            msg.textContent = `${type.charAt(0).toUpperCase() + type.slice(1)} driver updated to "${value}"`;
        })
        .catch(err => {
            console.error(err);
            alert('Failed to update driver.');
        });
    }
</script>

@endsection
