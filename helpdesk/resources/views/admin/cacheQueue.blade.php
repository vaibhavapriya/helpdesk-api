@extends('components.layouts.app.admin')

@section('content')
<div class="container">
    <h2>Two Tables with Radio Buttons</h2>
    <div class="row">
        <!-- Table 1 -->
        <div class="col-md-6">
            <h4>Table 1</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Name</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="radio" name="table1" value="user1" onchange="handleChange(this)"></td>
                        <td>Alice</td>
                        <td>Active</td>
                    </tr>
                    <tr>
                        <td><input type="radio" name="table1" value="user2" onchange="handleChange(this)"></td>
                        <td>Bob</td>
                        <td>Inactive</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Table 2 -->
        <div class="col-md-6">
            <h4>Table 2</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Name</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="radio" name="table2" value="user3" onchange="handleChange(this)"></td>
                        <td>Charlie</td>
                        <td>Active</td>
                    </tr>
                    <tr>
                        <td><input type="radio" name="table2" value="user4" onchange="handleChange(this)"></td>
                        <td>Diana</td>
                        <td>Inactive</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    function handleChange(radio) {
        const value = radio.value;
        const tableGroup = radio.name;

        console.log("Selected:", value, "from", tableGroup);

        // Send the selected value to backend
        fetch('/api/radio-selection', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // for Laravel
            },
            body: JSON.stringify({
                selected_user: value,
                table: tableGroup
            })
        })
        .then(response => response.json())
        .then(data => console.log('Response:', data))
        .catch(error => console.error('Error:', error));
    }
</script>
@endsection

<!-- <body class="p-4">
<div class="container">
  <h2>Change Queue Driver</h2>
  <div class="mb-3">
    <label for="queueSelect" class="form-label">Select Queue Driver:</label>
    <select id="queueSelect" class="form-select" onchange="updateQueueDriver()">
      <option value="database">Database</option>
      <option value="redis">Redis</option>
      <option value="sync">Sync</option>
      <option value="null">Null</option>
    </select>
  </div>
  <div id="statusMessage" class="alert alert-info d-none"></div>
</div>

<script>
    // Load current queue driver on page load
    fetch('/api/queue-driver')
        .then(res => res.json())
        .then(data => {
            const current = data.queue_driver;
            document.getElementById('queueSelect').value = current;
        });

    function updateQueueDriver() {
        const selected = document.getElementById('queueSelect').value;

        fetch('/api/queue-driver', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // if using web middleware
            },
            body: JSON.stringify({ queue_driver: selected })
        })
        .then(res => res.json())
        .then(data => {
            const msg = document.getElementById('statusMessage');
            msg.classList.remove('d-none');
            msg.textContent = `Queue driver changed to ${data.queue_driver}`;
        })
        .catch(err => alert('Error updating queue driver.'));
    }
</script>
</body> -->
