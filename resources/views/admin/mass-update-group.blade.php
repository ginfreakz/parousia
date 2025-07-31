@extends('admin.layout')

@section('content')

<h1 class="h3 mb-4 text-gray-800">Mass Update Group</h1>
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Select Group</h6>
            </div>
            <div class="card-body">
                <form id="contactHistoryForm" action="{{ url('/mass-update-history') }}" method="POST">
                    @csrf
                    <div class="form-group" style="width: 300px;">
                        <label for="group">Select Group</label>
                        <select id="group" name="group_id" class="form-control" required>
                            <option value="">-- Select Group --</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="membersContainer"></div>

                    <button type="submit" class="btn btn-primary mt-3">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('group').addEventListener('change', function() {
    let groupId = this.value;
    let membersContainer = document.getElementById('membersContainer');
    membersContainer.innerHTML = '';

    if (groupId) {
        fetch(`/get-members/${groupId}`)
            .then(response => response.json())
            .then(data => {
                const userRole = data.userRole; // Get logged-in user's role

                // Get today's date
                const today = new Date();
                const todayString = today.toISOString().split('T')[0]; // Format as YYYY-MM-DD

                // Calculate min date (7 days before today)
                const minDate = new Date(today);
                minDate.setDate(today.getDate() - 7);
                const minDateString = minDate.toISOString().split('T')[0];

                data.members.forEach(member => {
                    // Generate the skala options, removing lower options
                    let skalaOptions = "";
                    for (let i = member.latest_skala; i <= 3; i++) {
                        skalaOptions += `<option value="${i}" ${i === member.latest_skala ? 'selected' : ''}>${i}</option>`;
                    }

                    let memberForm = `
                        <div class="card p-3 mt-3">
                            <h5>${member.name}</h5>
                            <input type="hidden" name="members[${member.id}][id]" value="${member.id}">
                            <div class="form-group">
                                <label for="tanggal_${member.id}">Tanggal</label>
                                <input type="date" name="members[${member.id}][tanggal]" class="form-control"
                                    ${userRole != 7 ? `min="${minDateString}" max="${todayString}"` : ''}>
                            </div>
                            <div class="form-group">
                                <label for="skala_${member.id}">Skala</label>
                                <select name="members[${member.id}][skala]" class="form-control">
                                    ${skalaOptions}
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="description_${member.id}">Description</label>
                                <textarea name="members[${member.id}][description]" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    `;
                    document.getElementById('membersContainer').innerHTML += memberForm;
                });
            });
    }

});
</script>
@endsection
