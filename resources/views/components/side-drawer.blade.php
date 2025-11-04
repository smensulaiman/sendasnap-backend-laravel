@props([
    'id' => 'drawer',
    'title' => null,
    'width' => '320px',
])

<div id="{{ $id }}-overlay" style="display:none; position: fixed; inset: 0; background: rgba(0,0,0,.35); z-index: 9998;" onclick="window.closeDrawer('{{ $id }}')"></div>

<aside id="{{ $id }}" style="display:none; position: fixed; top: 0; right: 0; height: 100vh; width: {{ $width }}; background: #ffffff; box-shadow: -20px 0 60px rgba(0,0,0,.15); border-left: 1px solid hsl(var(--border)); z-index: 9999; transform: translateX(100%); transition: transform .25s ease; display: flex; flex-direction: column;">
    <div class="d-flex align-items-center justify-content-between" style="height: 56px; padding: 0 14px; border-bottom: 1px solid hsl(var(--border));">
        <div class="title-md">{{ $title }}</div>
        <button type="button" class="btn btn-icon" onclick="window.closeDrawer('{{ $id }}')">
            <span class="material-symbols-rounded">close</span>
        </button>
    </div>
    <div style="flex:1; overflow:auto; padding: 14px; background: #ffffff;" class="drawer-body">
        {{ $slot }}
    </div>
</aside>

<script>
    window.openDrawer = function(id) {
        const drawer = document.getElementById(id);
        const overlay = document.getElementById(id + '-overlay');
        if (!drawer || !overlay) return;
        overlay.style.display = 'block';
        drawer.style.display = 'flex';
        requestAnimationFrame(() => {
            drawer.style.transform = 'translateX(0)';
        });
        document.body.style.overflow = 'hidden';
    }
    window.closeDrawer = function(id) {
        const drawer = document.getElementById(id);
        const overlay = document.getElementById(id + '-overlay');
        if (!drawer || !overlay) return;
        drawer.style.transform = 'translateX(100%)';
        setTimeout(() => {
            overlay.style.display = 'none';
            drawer.style.display = 'none';
            document.body.style.overflow = '';
        }, 250);
    }
</script>


