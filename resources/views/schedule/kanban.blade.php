@extends('layouts.app')

@section('title', 'Schedule · Kanban')

@section('content')
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;">
        @php($cols = ['pending' => 'Pending', 'running' => 'Running', 'completed' => 'Completed', 'cancelled' => 'Cancelled'])
        @foreach($cols as $key => $label)
            <x-card>
                <x-slot:title>
                    <div style="display:flex;align-items:center;justify-content:space-between;width:100%">
                        <span>{{ $label }}</span>
                        <x-badge>{{ $columns[$key]->count() }}</x-badge>
                    </div>
                </x-slot:title>
                <div id="col-{{ $key }}" class="kanban-col" data-status="{{ $key }}" style="min-height:60vh;display:flex;flex-direction:column;gap:10px;">
                    @foreach($columns[$key] as $task)
                        <div class="card kanban-item" data-id="{{ $task->id }}" style="padding:12px;">
                            <div class="title-md" style="font-size:16px;">{{ $task->title }}</div>
                            <div class="text-sm">{{ optional($task->assignedUser)->name }}</div>
                        </div>
                    @endforeach
                </div>
            </x-card>
        @endforeach
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.3/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const cols = document.querySelectorAll('.kanban-col');
            cols.forEach(col => {
                new Sortable(col, {
                    group: 'kanban',
                    animation: 150,
                    ghostClass: 'dragging',
                    onAdd: function(evt) {
                        const el = evt.item;
                        const id = el.getAttribute('data-id');
                        const status = evt.to.getAttribute('data-status');
                        fetch(`/api/v1/tasks/${id}/status`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json',
                                'Authorization': `Bearer ${window?.authToken ?? ''}`,
                            },
                            body: JSON.stringify({ status })
                        }).then(async r => {
                            if (!r.ok) throw new Error('Failed');
                            showToast('Status updated', 'success');
                        }).catch(() => {
                            showToast('Could not update status', 'error');
                            evt.from.appendChild(el);
                        });
                    }
                });
            });
        });
    </script>
@endsection


