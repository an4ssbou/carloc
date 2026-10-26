@props(['notifications', 'unreadCount' => 0])

<div class="notification-menu"
    x-data="{
        expanded: false,
        toggle() {
            if (this.expanded) return this.close(true);
            this.expanded = true;
            this.$nextTick(() => this.$refs.closeButton.focus());
        },
        close(restoreFocus = false) {
            if (!this.expanded) return;
            this.expanded = false;
            if (restoreFocus) this.$refs.trigger.focus();
        }
    }"
    x-id="['notification-panel', 'notification-heading']"
    @keydown.escape.stop.prevent="close(true)"
    @click.outside="close()"
    @focusin.window="!$refs.panel.contains($event.target) && !$refs.trigger.contains($event.target) && close()">
    <button type="button" class="notification-bell" data-notification-trigger
        x-ref="trigger" @click="toggle()"
        aria-label="{{ $unreadCount > 0 ? 'Notifications, ' . $unreadCount . ' non lues' : 'Notifications' }}"
        aria-haspopup="dialog" aria-expanded="false" :aria-expanded="expanded"
        :aria-controls="$id('notification-panel')">
        <svg width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/></svg>
        @if($unreadCount > 0)
            <span class="notification-count" aria-hidden="true" data-notification-count="{{ $unreadCount }}">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
        @endif
    </button>

    <section class="notification-panel" data-notification-panel
        x-ref="panel" x-show="expanded" x-cloak style="display:none"
        :id="$id('notification-panel')" :aria-labelledby="$id('notification-heading')"
        role="dialog" aria-modal="false" aria-hidden="true" :aria-hidden="!expanded">
        <header class="notification-panel-heading">
            <div>
                <h2 :id="$id('notification-heading')">Notifications</h2>
                <p>{{ $unreadCount }} {{ $unreadCount === 1 ? 'non lue' : 'non lues' }}</p>
            </div>
            <button type="button" class="notification-close" x-ref="closeButton" @click="close(true)" aria-label="Fermer les notifications">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true" focusable="false"><path d="m6 6 12 12M18 6 6 18"/></svg>
            </button>
        </header>
        <ul class="notification-list" aria-label="Dernières notifications">
            @forelse($notifications as $notification)
                <li class="notification-preview {{ $notification->read_at === null ? 'notification-preview-unread' : '' }}" data-notification-item>
                    <div class="notification-preview-title">
                        <h3>{{ \Illuminate\Support\Str::limit((string) ($notification->data['title'] ?? 'Notification'), 100) }}</h3>
                        @if($notification->read_at === null)<span class="notification-unread-label">Non lue</span>@endif
                    </div>
                    <p>{{ \Illuminate\Support\Str::limit((string) ($notification->data['message'] ?? ''), 140) }}</p>
                    <time datetime="{{ $notification->created_at?->toIso8601String() }}">{{ $notification->created_at?->diffForHumans() }}</time>
                </li>
            @empty
                <li class="notification-empty">
                    <strong>Aucune notification</strong>
                    <p>Vos prochaines notifications apparaîtront ici.</p>
                </li>
            @endforelse
        </ul>
        <footer class="notification-panel-footer">
            <a href="{{ route('notifications.index') }}" aria-label="Voir toutes les notifications">Voir tout<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></a>
        </footer>
    </section>
</div>
