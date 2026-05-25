<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Face Attendance — {{ $classroom->room_name }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --red: #8B0000;
      --red-light: #a50000;
      --gold: #FFD700;
      --gold-light: #FFE44D;
      --white: #ffffff;
      --gray: #f5f5f5;
      --text: #333333;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Poppins', Arial, sans-serif; background: var(--gray); color: var(--text); }

    .navbar {
      background: var(--red);
      padding: 0 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      height: 64px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
      border-bottom: 3px solid var(--gold);
    }
    .navbar-brand { color: white; font-size: 17px; font-weight: 700; display: flex; align-items: center; gap: 10px; }
    .navbar-brand span { color: var(--gold); }
    .navbar-links a {
      color: var(--gold-light);
      text-decoration: none;
      font-size: 14px;
      padding: 6px 14px;
      border-radius: 5px;
      border: 1px solid rgba(255,215,0,0.35);
      transition: all 0.2s;
    }
    .navbar-links a:hover { background: rgba(255,255,255,0.1); color: white; border-color: var(--gold); }

    .container { max-width: 1000px; margin: 30px auto; padding: 0 20px; }

    .card {
      background: var(--white);
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
      margin-bottom: 20px;
    }
    .card h3 {
      color: var(--red);
      margin-bottom: 15px;
      font-size: 15px;
      border-bottom: 2px solid var(--gold);
      padding-bottom: 8px;
    }

    /* Class info */
    .class-info { text-align: center; padding: 8px; }
    .class-info h2 { font-size: 20px; color: var(--red); }
    .class-info p { color: #666; margin-top: 4px; font-size: 13px; }
    .time-badge {
      display: inline-block;
      padding: 5px 16px;
      border-radius: 20px;
      font-weight: 600;
      margin-top: 10px;
      font-size: 12px;
    }
    .badge-open { background: #d4edda; color: #155724; }
    .badge-late { background: #fff3cd; color: #856404; }
    .badge-no-class { background: #e2e3e5; color: #383d41; }

    /* Camera */
    .video-wrapper { position: relative; display: inline-block; width: 100%; }
    video { border: 3px solid var(--red); border-radius: 8px; width: 100%; display: block; }
    canvas#overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }

    #status {
      margin: 12px 0 6px;
      font-size: 15px;
      font-weight: 600;
      color: var(--red);
      text-align: center;
      min-height: 24px;
    }

    .scan-btn {
      padding: 13px 30px;
      background: var(--red);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 15px;
      font-family: 'Poppins', sans-serif;
      font-weight: 600;
      cursor: pointer;
      margin-top: 8px;
      width: 100%;
      transition: background 0.2s;
    }
    .scan-btn:hover:not(:disabled) { background: var(--red-light); }
    .scan-btn:disabled { background: #bbb; cursor: not-allowed; }

    /* Register */
    .register-form { margin-top: 10px; }
    .register-form input {
      width: 100%;
      padding: 10px 14px;
      margin-bottom: 10px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 14px;
      font-family: 'Poppins', sans-serif;
      outline: none;
      transition: border 0.2s;
    }
    .register-form input:focus { border-color: var(--red); }
    .register-btn {
      padding: 11px 20px;
      background: var(--gold);
      color: var(--red);
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-family: 'Poppins', sans-serif;
      font-weight: 700;
      cursor: pointer;
      width: 100%;
      transition: background 0.2s;
    }
    .register-btn:hover { background: var(--gold-light); }

    @media (max-width: 768px) {
      .navbar { padding: 10px 15px; height: auto; }
      .container { padding: 0 10px; margin: 15px auto; }
      .card { padding: 15px; }
    }
  </style>
</head>
<body>

  <div class="navbar">
    <div class="navbar-brand">
      🏫 <span>{{ $classroom->room_name }}</span>
      @if($classroom->room_code)
        &nbsp;<small style="font-weight:400;opacity:0.75;font-size:13px;">({{ $classroom->room_code }})</small>
      @endif
    </div>
    <div class="navbar-links">
      <a href="{{ route('room.select') }}">← Back to Rooms</a>
    </div>
  </div>

  <div class="container">

    <!-- Current Class Info -->
    <div class="card">
      <div class="class-info" id="classInfo">
        <p style="color:#aaa;">Loading schedule...</p>
      </div>
    </div>

    <!-- Camera -->
    <div class="card" style="text-align:center;">
      <h3>Face Scanner</h3>
      <div class="video-wrapper">
        <video id="video" autoplay muted playsinline></video>
        <canvas id="overlay"></canvas>
      </div>
      <div id="status">Loading models...</div>
      <button class="scan-btn" id="scanBtn" onclick="markAttendance()" disabled>
        Scan &amp; Mark Attendance
      </button>
    </div>

    <!-- Register New Student -->
    <div class="card">
      <h3>Register New Student</h3>
      <div class="register-form">
        <input type="text" id="idNumber" placeholder="Student ID Number" />
        <input type="text" id="studentName" placeholder="Full Name" />
        <button class="register-btn" id="registerBtn" onclick="registerFace()" disabled>📸 Register Face</button>
      </div>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
  <script>
    const classroomId   = {{ $classroom->id }};
    const video         = document.getElementById('video');
    const overlay       = document.getElementById('overlay');
    const ctx           = overlay.getContext('2d');
    const statusEl      = document.getElementById('status');
    const scanBtn       = document.getElementById('scanBtn');
    const registerBtn   = document.getElementById('registerBtn');
    const csrfToken     = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let currentSchedule   = null;
    let attendanceStatus  = 'closed';
    let currentSubjectCode = null;

    // ── Load current class from API ──────────────────────────────────────────
    async function loadCurrentClass() {
      try {
        const res  = await fetch(`/room/${classroomId}/current-class`);
        const data = await res.json();
        const el   = document.getElementById('classInfo');

        if (data.error) {
          el.innerHTML = `
            <h2>No Active Class</h2>
            <p>There is no class scheduled right now.</p>
            <span class="time-badge badge-no-class">CLOSED</span>`;
          scanBtn.disabled    = true;
          attendanceStatus    = 'closed';
          currentSubjectCode  = null;
          return;
        }

        currentSchedule    = data.schedule;
        attendanceStatus   = data.status;
        currentSubjectCode = data.schedule.subject_code;

        const label = attendanceStatus === 'open' ? 'ON TIME' : 'LATE';
        const cls   = attendanceStatus === 'open' ? 'badge-open' : 'badge-late';

        el.innerHTML = `
          <h2>${data.schedule.subject_name}</h2>
          <p>${data.schedule.subject_code} | Prof. ${data.schedule.professor_name}</p>
          <p>🏫 ${data.schedule.room} | ${data.schedule.day} ${data.schedule.time_in} – ${data.schedule.time_out}</p>
          <p style="font-size:12px;color:#888;margin-top:4px;">On time until: ${data.late_threshold}</p>
          <span class="time-badge ${cls}">${label}</span>`;

        scanBtn.disabled = false;
      } catch (e) {
        console.error('Schedule fetch error:', e);
      }
    }

    // ── Boot camera + models ─────────────────────────────────────────────────
    async function main() {
      const MODEL_URL = '/models';
      try {
        await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
        await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
        await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
      } catch (e) {
        statusEl.textContent = '❌ Failed to load face models. Make sure /public/models is present.';
        return;
      }

      statusEl.textContent = '✅ Models loaded — starting camera...';

      try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
      } catch (e) {
        statusEl.textContent = '❌ Camera access denied. Please allow camera permissions.';
        return;
      }

      video.addEventListener('play', () => {
        overlay.width  = video.offsetWidth;
        overlay.height = video.offsetHeight;
        statusEl.textContent = '✅ Ready!';
        registerBtn.disabled = false;
        drawFaceBox();
      });

      loadCurrentClass();
      setInterval(loadCurrentClass, 30000);
    }

    // ── Draw bounding box around detected face ───────────────────────────────
    async function drawFaceBox() {
      const displaySize = { width: video.offsetWidth, height: video.offsetHeight };
      faceapi.matchDimensions(overlay, displaySize);

      setInterval(async () => {
        const detection = await faceapi
          .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
          .withFaceLandmarks();

        ctx.clearRect(0, 0, overlay.width, overlay.height);

        if (detection) {
          const resized = faceapi.resizeResults(detection, displaySize);
          const { x, y, width, height } = resized.detection.box;
          ctx.strokeStyle = '#FFD700';
          ctx.lineWidth   = 3;
          ctx.strokeRect(x, y, width, height);
          ctx.fillStyle = '#FFD700';
          ctx.font      = '14px Poppins';
          ctx.fillText('Face Detected', x, y > 14 ? y - 6 : 14);
        }
      }, 200);
    }

    // ── Register face ────────────────────────────────────────────────────────
    async function registerFace() {
      const idNumber = document.getElementById('idNumber').value.trim();
      const name     = document.getElementById('studentName').value.trim();

      if (!idNumber) { statusEl.textContent = '⚠️ Please enter a Student ID Number.'; return; }
      if (!name)     { statusEl.textContent = '⚠️ Please enter the student\'s name.'; return; }
      if (!video.srcObject || video.readyState < HTMLMediaElement.HAVE_CURRENT_DATA) {
        statusEl.textContent = '⚠️ Camera is not ready yet. Please wait a moment and try again.';
        return;
      }

      statusEl.textContent = '📸 Capturing face...';
      registerBtn.disabled = true;

      try {
        const detection = await faceapi
          .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
          .withFaceLandmarks()
          .withFaceDescriptor();

        if (!detection) {
          statusEl.textContent = '❌ No face detected! Please look at the camera and try again.';
          return;
        }

        const descriptor = Array.from(detection.descriptor);

        const sendRegistration = async (confirmOverwrite = false) => {
          const res = await fetch('/register-face', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body:    JSON.stringify({
              id_number: idNumber,
              name,
              descriptor,
              confirm_overwrite: confirmOverwrite ? 1 : 0,
            }),
          });

          const data = await res.json();
          return { res, data };
        };

        let { data } = await sendRegistration(false);

        if (!data.success && data.requires_confirmation) {
          const proceed = confirm(
            `${data.message}\nUsed: ${data.registration_count}/3 registration slots. Continue?`
          );

          if (!proceed) {
            statusEl.textContent = '⚠️ Re-registration canceled.';
            return;
          }

          ({ data } = await sendRegistration(true));

          if (!data.success && data.requires_confirmation) {
            statusEl.textContent = '⚠️ Could not confirm re-registration. Please retry once.';
            return;
          }
        }

        statusEl.textContent = data.success
          ? `✅ ${name} registered successfully! (${data.registration_count}/3)`
          : `⚠️ ${data.message}`;

        if (data.success) {
          document.getElementById('idNumber').value    = '';
          document.getElementById('studentName').value = '';
        }
      } catch (error) {
        console.error('Face registration error:', error);
        statusEl.textContent = '❌ Face capture failed. Please check the camera and try again.';
      } finally {
        registerBtn.disabled = false;
      }
    }

    // ── Mark attendance ──────────────────────────────────────────────────────
    async function markAttendance() {
      if (!currentSchedule || attendanceStatus === 'closed') {
        statusEl.textContent = '❌ No active class right now!';
        return;
      }

      statusEl.textContent = '🔍 Scanning face...';

      const detection = await faceapi
        .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
        .withFaceLandmarks()
        .withFaceDescriptor();

      if (!detection) {
        statusEl.textContent = '❌ No face detected! Please try again.';
        return;
      }

      // Fetch all registered faces
      const facesRes = await fetch('/get-faces');
      const faces    = await facesRes.json();

      if (faces.length === 0) {
        statusEl.textContent = '❌ No registered faces found!';
        return;
      }

      // Find best match
      const descriptor   = detection.descriptor;
      let bestMatch      = null;
      let bestDistance   = 0.6; // threshold

      faces.forEach(face => {
        const saved    = new Float32Array(face.descriptor);
        const distance = faceapi.euclideanDistance(descriptor, saved);
        if (distance < bestDistance) {
          bestDistance = distance;
          bestMatch    = face;
        }
      });

      if (!bestMatch) {
        statusEl.textContent = '❌ Face not recognized! Please register first.';
        return;
      }

      // Log attendance
      const logRes  = await fetch('/log-attendance', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body:    JSON.stringify({
          face_id:           bestMatch.id,
          id_number:         bestMatch.id_number,
          name:              bestMatch.name,
          subject_code:      currentSubjectCode,
          attendance_status: attendanceStatus,
        }),
      });
      const logData = await logRes.json();

      statusEl.textContent = logData.success ? `✅ ${logData.message}` : `⚠️ ${logData.message}`;
    }

    main();
  </script>
</body>
</html>