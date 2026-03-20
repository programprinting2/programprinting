<nav class="navbar">
  <a href="#" class="sidebar-toggler">
    <i data-feather="menu"></i>
  </a>
  <div class="navbar-content">
    <form class="search-form">
      <div class="input-group">
        <div class="input-group-text">
          <i data-feather="search"></i>
        </div>
        <input type="text" class="form-control" id="navbarForm" placeholder="Search here...">
      </div>
    </form>
    <ul class="navbar-nav">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <img src="{{ url('assets/images/flags/us.svg') }}" class="wd-20 me-1" title="us" alt="us"> <span class="ms-1 me-1 d-none d-md-inline-block">English</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="languageDropdown">
          <a href="javascript:;" class="dropdown-item py-2"> <img src="{{ url('assets/images/flags/us.svg') }}" class="wd-20 me-1" title="us" alt="us"> <span class="ms-1"> English </span></a>
          <a href="javascript:;" class="dropdown-item py-2"> <img src="{{ url('assets/images/flags/fr.svg') }}" class="wd-20 me-1" title="fr" alt="fr"> <span class="ms-1"> French </span></a>
          <a href="javascript:;" class="dropdown-item py-2"> <img src="{{ url('assets/images/flags/de.svg') }}" class="wd-20 me-1" title="de" alt="de"> <span class="ms-1"> German </span></a>
          <a href="javascript:;" class="dropdown-item py-2"> <img src="{{ url('assets/images/flags/pt.svg') }}" class="wd-20 me-1" title="pt" alt="pt"> <span class="ms-1"> Portuguese </span></a>
          <a href="javascript:;" class="dropdown-item py-2"> <img src="{{ url('assets/images/flags/es.svg') }}" class="wd-20 me-1" title="es" alt="es"> <span class="ms-1"> Spanish </span></a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="appsDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i data-feather="grid"></i>
        </a>
        <div class="dropdown-menu p-0" aria-labelledby="appsDropdown">
          <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
            <p class="mb-0 fw-bold">Web Apps</p>
            <a href="javascript:;" class="text-muted">Edit</a>
          </div>
          <div class="row g-0 p-1">
            <div class="col-3 text-center">
              <a href="{{ url('/apps/chat') }}" class="dropdown-item d-flex flex-column align-items-center justify-content-center wd-70 ht-70"><i data-feather="message-square" class="icon-lg mb-1"></i><p class="tx-12">Chat</p></a>
            </div>
            <div class="col-3 text-center">
              <a href="{{ url('/apps/calendar') }}" class="dropdown-item d-flex flex-column align-items-center justify-content-center wd-70 ht-70"><i data-feather="calendar" class="icon-lg mb-1"></i><p class="tx-12">Calendar</p></a>
            </div>
            <div class="col-3 text-center">
              <a href="{{ url('/email/inbox') }}" class="dropdown-item d-flex flex-column align-items-center justify-content-center wd-70 ht-70"><i data-feather="mail" class="icon-lg mb-1"></i><p class="tx-12">Email</p></a>
            </div>
            <div class="col-3 text-center">
              <a href="{{ url('/general/profile') }}" class="dropdown-item d-flex flex-column align-items-center justify-content-center wd-70 ht-70"><i data-feather="instagram" class="icon-lg mb-1"></i><p class="tx-12">Profile</p></a>
            </div>
          </div>
          <div class="px-3 py-2 d-flex align-items-center justify-content-center border-top">
            <a href="javascript:;">View all</a>
          </div>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="messageDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i data-feather="mail"></i>
        </a>
        <div class="dropdown-menu p-0" aria-labelledby="messageDropdown">
          <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
            <p>9 New Messages</p>
            <a href="javascript:;" class="text-muted">Clear all</a>
          </div>
          <div class="p-1">
            <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
              <div class="me-3">
                <img class="wd-30 ht-30 rounded-circle" src="{{ url('https://via.placeholder.com/30x30') }}" alt="userr">
              </div>
              <div class="d-flex justify-content-between flex-grow-1">
                <div class="me-4">
                  <p>Leonardo Payne</p>
                  <p class="tx-12 text-muted">Project status</p>
                </div>
                <p class="tx-12 text-muted">2 min ago</p>
              </div>	
            </a>
            <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
              <div class="me-3">
                <img class="wd-30 ht-30 rounded-circle" src="{{ url('https://via.placeholder.com/30x30') }}" alt="userr">
              </div>
              <div class="d-flex justify-content-between flex-grow-1">
                <div class="me-4">
                  <p>Carl Henson</p>
                  <p class="tx-12 text-muted">Client meeting</p>
                </div>
                <p class="tx-12 text-muted">30 min ago</p>
              </div>	
            </a>
            <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
              <div class="me-3">
                <img class="wd-30 ht-30 rounded-circle" src="{{ url('https://via.placeholder.com/30x30') }}" alt="userr">
              </div>
              <div class="d-flex justify-content-between flex-grow-1">
                <div class="me-4">
                  <p>Jensen Combs</p>
                  <p class="tx-12 text-muted">Project updates</p>
                </div>
                <p class="tx-12 text-muted">1 hrs ago</p>
              </div>	
            </a>
            <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
              <div class="me-3">
                <img class="wd-30 ht-30 rounded-circle" src="{{ url('https://via.placeholder.com/30x30') }}" alt="userr">
              </div>
              <div class="d-flex justify-content-between flex-grow-1">
                <div class="me-4">
                  <p>Amiah Burton</p>
                  <p class="tx-12 text-muted">Project deatline</p>
                </div>
                <p class="tx-12 text-muted">2 hrs ago</p>
              </div>	
            </a>
            <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
              <div class="me-3">
                <img class="wd-30 ht-30 rounded-circle" src="{{ url('https://via.placeholder.com/30x30') }}" alt="userr">
              </div>
              <div class="d-flex justify-content-between flex-grow-1">
                <div class="me-4">
                  <p>Yaretzi Mayo</p>
                  <p class="tx-12 text-muted">New record</p>
                </div>
                <p class="tx-12 text-muted">5 hrs ago</p>
              </div>	
            </a>
          </div>
          <div class="px-3 py-2 d-flex align-items-center justify-content-center border-top">
            <a href="javascript:;">View all</a>
          </div>
        </div>
      </li>
      @php
        $user = auth()->user() ?? \App\Models\User::first(); 
        $unreadCount = $user->unreadNotifications()->count();
        $latestNotifs = $user->notifications()->latest()->limit(10)->get();
      @endphp
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button"
          data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i data-feather="bell"></i>
          <div class="indicator" id="notifIndicator" style="{{ $unreadCount ? '' : 'display:none;' }}">
            <div class="circle"></div>
          </div>
        </a>
        <div class="dropdown-menu p-0" aria-labelledby="notificationDropdown" style="min-width: 360px;">
          <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
            <p class="mb-0"><span id="notifCount">{{ $unreadCount }}</span> Notifikasi baru</p>
            <a href="javascript:;" class="text-muted" id="notifClearAll">Clear all</a>
          </div>
          <div class="p-1" id="notifList">
            @forelse($latestNotifs as $n)
              @php
                $data = $n->data ?? [];
                $action = $data['action'] ?? '';
                $title = $action === 'created'
                  ? 'SPK baru dibuat'
                  : ($action === 'status_changed' ? 'Status SPK berubah' : 'Update SPK');
                $nomor = $data['nomor_spk'] ?? ('#'.$data['spk_id'] ?? '-');
                $sub = trim(($data['pelanggan'] ?? '').' • '.(($data['status'] ?? '') ? ('Status: '.$data['status']) : ''));
                $isUnread = is_null($n->read_at);
              @endphp
              <a href="{{ route('spk.show', $data['nomor_spk'] ?? 0) }}"
                class="dropdown-item d-flex align-items-center py-2 {{ $isUnread ? 'bg-light' : '' }}"
                data-notif-id="{{ $n->id }}">
                <div class="wd-30 ht-30 d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                  <i class="icon-sm text-white" data-feather="{{ $action === 'created' ? 'plus-circle' : 'refresh-cw' }}"></i>
                </div>
                <div class="flex-grow-1 me-2">
                  <p class="mb-0">{{ $title }}: {{ $nomor }}</p>
                  <p class="tx-12 text-muted mb-0">{{ $sub ?: '-' }}</p>
                </div>
                <p class="tx-12 text-muted mb-0">{{ $n->created_at?->diffForHumans() }}</p>
              </a>
            @empty
              <div class="text-center text-muted py-3">Tidak ada notifikasi.</div>
            @endforelse
          </div>
          <div class="px-3 py-2 d-flex align-items-center justify-content-center border-top">
            <a href="{{ route('pekerjaan.manager-order') }}">View all</a>
          </div>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <img class="wd-30 ht-30 rounded-circle" src="{{ url('https://placehold.co/30x30') }}" alt="profile">
        </a>
        <div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
          <div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
            <div class="mb-3">
              <img class="wd-80 ht-80 rounded-circle" src="{{ url('https://placehold.co/80x80') }}" alt="">
            </div>
            <div class="text-center">
              <p class="tx-16 fw-bolder">{{ auth()->user()->name }}</p>
              <p class="tx-12 text-muted">{{ auth()->user()->email }}</p>
            </div>
          </div>
          <ul class="list-unstyled p-1">
            <li class="dropdown-item py-2">
              <a href="{{ url('/general/profile') }}" class="text-body ms-0">
                <i class="me-2 icon-md" data-feather="user"></i>
                <span>Profile</span>
              </a>
            </li>
            <li class="dropdown-item py-2">
              <a href="javascript:;" class="text-body ms-0">
                <i class="me-2 icon-md" data-feather="edit"></i>
                <span>Edit Profile</span>
              </a>
            </li>
            <li class="dropdown-item py-2">
              <a href="javascript:;" class="text-body ms-0">
                <i class="me-2 icon-md" data-feather="repeat"></i>
                <span>Switch User</span>
              </a>
            </li>
            <li class="dropdown-item py-2">
              <form id="logout-form" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-body ms-0 w-100 text-start border-0 bg-transparent p-0 d-flex align-items-center">
                  <i class="me-2 icon-md" data-feather="log-out"></i>
                  <span>Log Out</span>
                </button>
              </form>
            </li>
          </ul>
        </div>
      </li>
    </ul>
  </div>
</nav>

<script>
(function () {
  const indicator = document.getElementById('notifIndicator');
  const countEl = document.getElementById('notifCount');
  const listEl = document.getElementById('notifList');
  const clearBtn = document.getElementById('notifClearAll');

  if (!countEl || !listEl) return;

  function setCount(n) {
    countEl.textContent = String(n);
    if (indicator) indicator.style.display = n > 0 ? '' : 'none';
  }

  function escapeHtml(s) {
    return String(s ?? '').replace(/[&<>"']/g, (m) => ({
      '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'
    }[m]));
  }

  function prependNotif(payload) {
    const action = payload.action || '';
    const title = action === 'created'
      ? 'SPK baru dibuat'
      : (action === 'status_changed' ? 'Status SPK berubah' : 'Update SPK');

    const nomor = payload.nomor_spk || ('#' + payload.spk_id);
    const subParts = [];
    if (payload.pelanggan) subParts.push(payload.pelanggan);
    if (payload.status) subParts.push('Status: ' + payload.status);
    const sub = subParts.join(' • ') || '-';

    const icon = action === 'created' ? 'plus-circle' : 'refresh-cw';
    const href = "{{ route('spk.show', 0) }}".replace(/0$/, encodeURIComponent(payload.spk_id || 0));

    const itemHtml = `
      <a href="${href}" class="dropdown-item d-flex align-items-center py-2 bg-light">
        <div class="wd-30 ht-30 d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
          <i class="icon-sm text-white" data-feather="${icon}"></i>
        </div>
        <div class="flex-grow-1 me-2">
          <p class="mb-0">${escapeHtml(title)}: ${escapeHtml(nomor)}</p>
          <p class="tx-12 text-muted mb-0">${escapeHtml(sub)}</p>
        </div>
        <p class="tx-12 text-muted mb-0">baru</p>
      </a>
    `;

    // kalau list kosong "Tidak ada notifikasi", replace
    if (listEl.textContent.includes('Tidak ada notifikasi')) {
      listEl.innerHTML = itemHtml;
    } else {
      listEl.insertAdjacentHTML('afterbegin', itemHtml);
    }

    if (window.feather) feather.replace();

    const current = parseInt(countEl.textContent || '0', 10) || 0;
    setCount(current + 1);
  }

  if (clearBtn) {
    clearBtn.addEventListener('click', async (e) => {
      e.preventDefault();
      const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const res = await fetch("{{ route('notifications.markAllRead') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
      });
      if (res.ok) {
        setCount(0);
        // optionally: hilangkan highlight bg-light
        listEl.querySelectorAll('.dropdown-item.bg-light').forEach(el => el.classList.remove('bg-light'));
      }
    });
  }

  // realtime: private channel user
  const userId = @json(auth()->id());
  if (userId && window.Echo) {
    window.Echo.private(`App.User.${userId}`)
      .notification((notification) => {
        prependNotif(notification);
      });
  }
})();
</script>

<script>
(function () {
  document.addEventListener('click', async (e) => {
    const a = e.target.closest('a[data-notif-id]');
    if (!a) return;

    const id = a.getAttribute('data-notif-id');
    if (!id) return;

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // mark as read tanpa mengganggu navigasi
    try {
      await fetch("{{ route('notifications.read', ['id' => '__ID__']) }}".replace('__ID__', encodeURIComponent(id)), {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
      });
    } catch (_) {}
  });
})();
</script>