<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Select Room — Attendance System</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root { --red: #8B0000; --gold: #FFD700; --text: #333; }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Poppins', sans-serif; min-height: 100vh; background: linear-gradient(135deg, #8B0000 0%, #4a0000 100%); }

    .topbar {
      background: rgba(0,0,0,0.25);
      padding: 14px 30px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      backdrop-filter: blur(6px);
    }
    .topbar .brand { color: #FFD700; font-weight: 700; font-size: 1rem; letter-spacing: 0.5px; }
    .topbar .login-btn {
      color: white;
      text-decoration: none;
      font-size: 0.85rem;
      font-weight: 500;
      padding: 7px 18px;
      border: 1px solid rgba(255,255,255,0.4);
      border-radius: 20px;
      transition: all 0.2s;
    }
    .topbar .login-btn:hover { background: rgba(255,255,255,0.15); border-color: #FFD700; color: #FFD700; }

    .room-wrapper {
      min-height: calc(100vh - 56px);
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 40px 20px 60px;
    }

    .page-title { color: white; font-size: 1.6rem; font-weight: 700; margin-bottom: 6px; text-align: center; }
    .page-sub { color: rgba(255,255,255,0.65); font-size: 0.88rem; margin-bottom: 28px; text-align: center; }

    .search-box { width: 100%; max-width: 900px; margin-bottom: 32px; position: relative; }
    .search-box input {
      width: 100%;
      padding: 13px 20px 13px 46px;
      border: 2px solid rgba(255,255,255,0.2);
      border-radius: 30px;
      font-size: 15px;
      font-family: 'Poppins', sans-serif;
      outline: none;
      background: rgba(255,255,255,0.12);
      color: white;
      transition: border 0.2s, background 0.2s;
    }
    .search-box input::placeholder { color: rgba(255,255,255,0.5); }
    .search-box input:focus { border-color: #FFD700; background: rgba(255,255,255,0.18); }
    .search-box .search-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); font-size: 18px; }

    .building-group { width: 100%; max-width: 900px; margin-bottom: 28px; }
    .building-label {
      font-size: 12px;
      font-weight: 600;
      color: #FFD700;
      text-transform: uppercase;
      letter-spacing: 1px;
      border-left: 4px solid #FFD700;
      padding-left: 10px;
      margin-bottom: 12px;
    }

    .grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; }
    .room-card {
      background: rgba(255,255,255,0.95);
      border-radius: 12px;
      padding: 18px 10px;
      text-align: center;
      text-decoration: none;
      color: var(--text);
      border-bottom: 3px solid transparent;
      transition: all 0.2s;
      box-shadow: 0 2px 12px rgba(0,0,0,0.15);
    }
    .room-card:hover { border-bottom: 3px solid #FFD700; transform: translateY(-4px); background: var(--red); color: white; box-shadow: 0 8px 24px rgba(0,0,0,0.25); }
    .room-card .icon { font-size: 28px; }
    .room-card h3 { font-size: 13px; margin-top: 7px; font-weight: 600; }
    .room-card .code { font-size: 11px; opacity: 0.6; margin-top: 2px; }
    .room-card.hidden { display: none; }

    .no-results { text-align: center; color: rgba(255,255,255,0.6); padding: 30px; width: 100%; display: none; font-size: 0.95rem; }
    .empty-state { text-align: center; color: rgba(255,255,255,0.65); padding: 40px 20px; }
    .empty-state .icon { font-size: 48px; margin-bottom: 12px; }

    @media (max-width: 768px) { .grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 480px) { .grid { grid-template-columns: repeat(2, 1fr); } }
  </style>
</head>
<body>

  <div class="topbar">
    <span class="brand">🏫 Attendance System</span>
    <a href="{{ route('login') }}" class="login-btn">Staff Login →</a>
  </div>

  <div class="room-wrapper">
    <h1 class="page-title">Select a Room</h1>
    <p class="page-sub">Tap your room to begin attendance</p>

    <div class="search-box">
      <span class="search-icon">🔍</span>
      <input type="text" id="roomSearch" placeholder="Search room or building..." oninput="filterRooms()" />
    </div>

    @php
      $grouped = $classrooms->groupBy('building')->sortByDesc(fn($rooms) => $rooms->count());
    @endphp

    @forelse($grouped as $building => $buildingRooms)
      <div class="building-group" id="group-{{ Str::slug($building ?: 'no-building') }}">
        <div class="building-label">🏢 {{ $building ?: 'No Building' }}</div>
        <div class="grid">
          @foreach($buildingRooms as $room)
            <a href="{{ route('face', $room->id) }}"
               class="room-card"
               data-name="{{ strtolower($room->room_name) }}"
               data-building="{{ strtolower($room->building ?? '') }}">
              <div class="icon">🏫</div>
              <h3>{{ $room->room_name }}</h3>
              @if($room->room_code)
                <div class="code">{{ $room->room_code }}</div>
              @endif
            </a>
          @endforeach
        </div>
      </div>
    @empty
      <div class="empty-state">
        <div class="icon">🏫</div>
        <p>No rooms available yet.<br>Please contact the administrator.</p>
      </div>
    @endforelse

    <p class="no-results" id="noResults">No rooms match your search.</p>
  </div>

  <script>
    function filterRooms() {
      const query = document.getElementById('roomSearch').value.toLowerCase().trim();
      const cards = document.querySelectorAll('.room-card');
      const groups = document.querySelectorAll('.building-group');
      let visibleCount = 0;

      if (query === '') {
        cards.forEach(card => card.classList.remove('hidden'));
        groups.forEach(group => group.style.display = 'block');
        document.getElementById('noResults').style.display = 'none';
        return;
      }

      groups.forEach(group => group.style.display = 'none');
      cards.forEach(card => {
        const name = card.dataset.name || '';
        const building = card.dataset.building || '';
        if (name.includes(query) || building.includes(query)) {
          card.classList.remove('hidden');
          card.closest('.building-group').style.display = 'block';
          visibleCount++;
        } else {
          card.classList.add('hidden');
        }
      });

      document.getElementById('noResults').style.display = visibleCount === 0 ? 'block' : 'none';
    }
  </script>
</body>
</html>