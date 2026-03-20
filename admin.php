<?php
// ============================================================
//  RH Catering – Admin Panel
//  Place this file in your website root on one.com
//  Change the password below before uploading!
// ============================================================

define('ADMIN_PASSWORD', 'rhcatering2024'); // <-- CHANGE THIS
define('CONTENT_FILE',   __DIR__ . '/content.json');
define('SESSION_KEY',    'rh_admin_logged_in');

session_start();

// ---------- Auth ----------
if (isset($_POST['password'])) {
    if ($_POST['password'] === ADMIN_PASSWORD) {
        $_SESSION[SESSION_KEY] = true;
    } else {
        $loginError = 'Forkert adgangskode. Prøv igen.';
    }
}
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}
$loggedIn = !empty($_SESSION[SESSION_KEY]);

// ---------- Load content ----------
function loadContent(): array {
    global $contentFile;
    if (file_exists(CONTENT_FILE)) {
        return json_decode(file_get_contents(CONTENT_FILE), true) ?? [];
    }
    // Default content matching the current site
    return [
        'contact' => [
            'address'   => 'Skovgårdsvejen 1, 3700 Rønne',
            'cvr'       => '45952983',
            'email'     => 'dorte@rhcatering.dk',
            'phone'     => '+45 42338230',
        ],
        'menu_items' => [
            [
                'title'       => 'Flæskesteg',
                'description' => 'Flæskesteg af dansk gris, letsaltet med sprøde svær',
                'sides'       => 'Hertil serveres: kogte kartofler, brun sauce, hjemmekogt rødkål og hjemmelavet sylt',
                'price'       => '165',
                'min_covers'  => '8',
            ],
            [
                'title'       => 'Kyllingesteg',
                'description' => 'Gammeldags kyllingesteg af dansk kylling',
                'sides'       => 'Hertil serveres: sprøde ovnkartofler, brun sauce med persille og hjemmelavet sylt',
                'price'       => '155',
                'min_covers'  => '8',
            ],
            [
                'title'       => 'Forloren Hare',
                'description' => 'Forloren hare af hakket kød fra dansk gris og kalv',
                'sides'       => 'Hertil serveres: kogte kartofler, vildtsauce, bønnesalat med feta og sprød bacon og hjemmesyltet agurk',
                'price'       => '155',
                'min_covers'  => '8',
            ],
            [
                'title'       => 'Helstegt Oksefilet',
                'description' => 'Helstegt oksefilet af korn opfedet kødkvæg med krølfedt',
                'sides'       => 'Hertil serveres: cremede flødekartofler med urter, kraftig kalve skysauce og let spidskålsalat med sort sesam',
                'price'       => '225',
                'min_covers'  => '8',
            ],
            [
                'title'       => 'Kalvesteg',
                'description' => 'Kalvesteg af dansk gourmet kalv',
                'sides'       => 'Hertil serveres: kogte kartofler, kalve skysauce, waldorfsalat og hjemmelavet sylt',
                'price'       => '185',
                'min_covers'  => '8',
            ],
            [
                'title'       => 'Kalvemørbrad',
                'description' => 'Rosastegt dansk kalvemørbrad af dansk gourmet kalv',
                'sides'       => 'Hertil serveres: flamberede skovsvampe, bagte urter, pommes Chateau, kraftig portvinssky og plukkede salater med citronolie',
                'price'       => '255',
                'min_covers'  => '8',
            ],
        ],
    ];
}

// ---------- Save content ----------
$saveMessage = '';
if ($loggedIn && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $content = loadContent();

    // Contact info
    $content['contact']['address'] = trim($_POST['address'] ?? '');
    $content['contact']['cvr']     = trim($_POST['cvr']     ?? '');
    $content['contact']['email']   = trim($_POST['email']   ?? '');
    $content['contact']['phone']   = trim($_POST['phone']   ?? '');

    // Menu items
    $content['menu_items'] = [];
    $titles       = $_POST['item_title']       ?? [];
    $descriptions = $_POST['item_description'] ?? [];
    $sides        = $_POST['item_sides']        ?? [];
    $prices       = $_POST['item_price']        ?? [];
    $minCovers    = $_POST['item_min_covers']   ?? [];

    foreach ($titles as $i => $title) {
        if (trim($title) === '') continue;
        $content['menu_items'][] = [
            'title'       => trim($title),
            'description' => trim($descriptions[$i] ?? ''),
            'sides'       => trim($sides[$i]        ?? ''),
            'price'       => trim($prices[$i]       ?? ''),
            'min_covers'  => trim($minCovers[$i]    ?? '8'),
        ];
    }

    file_put_contents(CONTENT_FILE, json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    $saveMessage = 'Ændringer gemt! Siden er nu opdateret.';
}

$content = loadContent();
$menuItems = $content['menu_items'] ?? [];
$contact   = $content['contact']   ?? [];
?>
<!DOCTYPE html>
<html lang="da">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>RH Catering – Admin</title>
<style>
  /* ---- Reset & Base ---- */
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --brand:   #F6C636;
    --bg:      #0d0d0d;
    --surface: #1a1a1a;
    --border:  #2e2e2e;
    --text:    #e8e8e8;
    --muted:   #888;
    --danger:  #e05252;
    --success: #52c07a;
    --radius:  10px;
  }

  body {
    font-family: system-ui, sans-serif;
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
  }

  /* ---- Login Page ---- */
  .login-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 20px;
  }

  .login-box {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 50px 40px;
    width: 100%;
    max-width: 400px;
    text-align: center;
  }

  .login-logo {
    font-size: 13px;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: var(--brand);
    margin-bottom: 8px;
  }

  .login-title {
    font-size: 26px;
    font-weight: 700;
    margin-bottom: 32px;
    color: white;
  }

  .login-box input[type="password"] {
    width: 100%;
    padding: 14px 18px;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    color: var(--text);
    font-size: 16px;
    margin-bottom: 14px;
    outline: none;
    transition: border-color 0.2s;
  }

  .login-box input[type="password"]:focus { border-color: var(--brand); }

  .btn-primary {
    width: 100%;
    padding: 14px;
    background: var(--brand);
    color: black;
    font-weight: 700;
    font-size: 15px;
    border: none;
    border-radius: var(--radius);
    cursor: pointer;
    transition: opacity 0.2s;
  }
  .btn-primary:hover { opacity: 0.85; }

  .error-msg {
    background: rgba(224,82,82,0.15);
    border: 1px solid var(--danger);
    color: var(--danger);
    border-radius: var(--radius);
    padding: 10px 14px;
    font-size: 14px;
    margin-bottom: 16px;
  }

  /* ---- Admin Shell ---- */
  .topbar {
    background: black;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 30px;
    height: 60px;
    position: sticky;
    top: 0;
    z-index: 50;
  }

  .topbar-brand {
    font-size: 12px;
    letter-spacing: 2.5px;
    text-transform: uppercase;
    color: var(--brand);
    font-weight: 700;
  }

  .topbar-right { display: flex; align-items: center; gap: 16px; }

  .btn-logout {
    background: transparent;
    border: 1px solid var(--border);
    color: var(--muted);
    padding: 7px 14px;
    border-radius: 6px;
    font-size: 13px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
  }
  .btn-logout:hover { border-color: var(--text); color: var(--text); }

  .btn-save-top {
    background: var(--brand);
    color: black;
    border: none;
    padding: 8px 20px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: opacity 0.2s;
  }
  .btn-save-top:hover { opacity: 0.85; }

  /* ---- Layout ---- */
  .admin-layout {
    display: grid;
    grid-template-columns: 220px 1fr;
    min-height: calc(100vh - 60px);
  }

  .sidebar {
    background: var(--surface);
    border-right: 1px solid var(--border);
    padding: 24px 0;
    position: sticky;
    top: 60px;
    height: calc(100vh - 60px);
    overflow-y: auto;
  }

  .sidebar-section {
    padding: 6px 20px 4px;
    font-size: 10px;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--muted);
    margin-top: 12px;
  }

  .sidebar a {
    display: block;
    padding: 9px 20px;
    color: var(--text);
    text-decoration: none;
    font-size: 14px;
    border-left: 3px solid transparent;
    transition: all 0.15s;
  }

  .sidebar a:hover, .sidebar a.active {
    background: rgba(246,198,54,0.08);
    border-left-color: var(--brand);
    color: var(--brand);
  }

  /* ---- Content Area ---- */
  .content-area {
    padding: 36px 40px;
    max-width: 900px;
  }

  .section-block {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 30px;
    margin-bottom: 28px;
  }

  .section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 22px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--border);
  }

  .section-title {
    font-size: 16px;
    font-weight: 700;
    color: white;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .section-title .icon {
    width: 28px; height: 28px;
    background: rgba(246,198,54,0.12);
    border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px;
  }

  /* ---- Form elements ---- */
  .field-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
  }

  .field-row.single { grid-template-columns: 1fr; }
  .field-row.triple { grid-template-columns: 1fr 1fr 1fr; }

  .field label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: var(--muted);
    margin-bottom: 7px;
  }

  .field input, .field textarea {
    width: 100%;
    padding: 11px 14px;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    color: var(--text);
    font-size: 14px;
    font-family: inherit;
    outline: none;
    transition: border-color 0.2s;
    resize: vertical;
  }

  .field input:focus, .field textarea:focus { border-color: var(--brand); }
  .field textarea { min-height: 70px; line-height: 1.5; }

  /* ---- Menu item cards ---- */
  .menu-item-card {
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 22px;
    margin-bottom: 16px;
    position: relative;
  }

  .menu-item-card .card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
  }

  .menu-item-card .card-num {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: var(--brand);
    background: rgba(246,198,54,0.1);
    padding: 3px 10px;
    border-radius: 20px;
  }

  .btn-remove {
    background: transparent;
    border: 1px solid #3a2020;
    color: var(--danger);
    padding: 5px 12px;
    border-radius: 6px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s;
  }
  .btn-remove:hover { background: rgba(224,82,82,0.1); }

  .btn-add {
    display: flex;
    align-items: center;
    gap: 8px;
    background: transparent;
    border: 1px dashed var(--border);
    color: var(--muted);
    padding: 12px 20px;
    border-radius: var(--radius);
    font-size: 14px;
    cursor: pointer;
    width: 100%;
    justify-content: center;
    transition: all 0.2s;
    margin-top: 4px;
  }
  .btn-add:hover { border-color: var(--brand); color: var(--brand); }

  /* ---- Toast / Success ---- */
  .toast {
    position: fixed;
    bottom: 28px;
    right: 28px;
    background: var(--surface);
    border: 1px solid var(--success);
    color: var(--success);
    padding: 14px 20px;
    border-radius: var(--radius);
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.4);
    animation: slideIn 0.3s ease, fadeOut 0.5s 3s ease forwards;
    z-index: 999;
  }

  @keyframes slideIn {
    from { transform: translateY(20px); opacity: 0; }
    to   { transform: translateY(0);    opacity: 1; }
  }
  @keyframes fadeOut {
    to { opacity: 0; transform: translateY(10px); }
  }

  /* ---- Page title ---- */
  .page-title {
    font-size: 24px;
    font-weight: 700;
    color: white;
    margin-bottom: 6px;
  }
  .page-subtitle {
    font-size: 14px;
    color: var(--muted);
    margin-bottom: 32px;
  }

  @media (max-width: 700px) {
    .admin-layout { grid-template-columns: 1fr; }
    .sidebar { display: none; }
    .content-area { padding: 24px 18px; }
    .field-row { grid-template-columns: 1fr; }
    .field-row.triple { grid-template-columns: 1fr 1fr; }
  }
</style>
</head>
<body>

<?php if (!$loggedIn): ?>
<!-- ===================== LOGIN ===================== -->
<div class="login-wrap">
  <div class="login-box">
    <div class="login-logo">RH Catering</div>
    <h1 class="login-title">Admin Panel</h1>
    <?php if (!empty($loginError)): ?>
      <div class="error-msg"><?= htmlspecialchars($loginError) ?></div>
    <?php endif; ?>
    <form method="POST">
      <input type="password" name="password" placeholder="Adgangskode" autofocus />
      <button type="submit" class="btn-primary">Log ind</button>
    </form>
  </div>
</div>

<?php else: ?>
<!-- ===================== ADMIN ===================== -->
<form method="POST">
<input type="hidden" name="save" value="1" />

<!-- Top bar -->
<div class="topbar">
  <div class="topbar-brand">RH Catering — Admin</div>
  <div class="topbar-right">
    <a href="?logout=1" class="btn-logout">Log ud</a>
    <button type="submit" class="btn-save-top">💾 Gem ændringer</button>
  </div>
</div>

<div class="admin-layout">
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="sidebar-section">Sider</div>
    <a href="#kontakt" class="active">📞 Kontaktoplysninger</a>
    <a href="#menu">🍽️ Hovedretter</a>
    <div class="sidebar-section">Links</div>
    <a href="index.html" target="_blank">🌐 Se hjemmeside</a>
    <a href="hovedretter.html" target="_blank">🔗 Se menuside</a>
  </aside>

  <!-- Main content -->
  <main class="content-area">
    <h1 class="page-title">Rediger indhold</h1>
    <p class="page-subtitle">Ændringer gemmes med det samme og vises på hjemmesiden.</p>

    <!-- Contact section -->
    <div class="section-block" id="kontakt">
      <div class="section-header">
        <div class="section-title">
          <div class="icon">📞</div>
          Kontaktoplysninger
        </div>
      </div>

      <div class="field-row">
        <div class="field">
          <label>Adresse</label>
          <input type="text" name="address" value="<?= htmlspecialchars($contact['address'] ?? '') ?>" />
        </div>
        <div class="field">
          <label>CVR-nummer</label>
          <input type="text" name="cvr" value="<?= htmlspecialchars($contact['cvr'] ?? '') ?>" />
        </div>
      </div>
      <div class="field-row">
        <div class="field">
          <label>Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars($contact['email'] ?? '') ?>" />
        </div>
        <div class="field">
          <label>Telefonnummer</label>
          <input type="text" name="phone" value="<?= htmlspecialchars($contact['phone'] ?? '') ?>" />
        </div>
      </div>
    </div>

    <!-- Menu items section -->
    <div class="section-block" id="menu">
      <div class="section-header">
        <div class="section-title">
          <div class="icon">🍽️</div>
          Hovedretter
        </div>
      </div>

      <div id="menu-items-container">
        <?php foreach ($menuItems as $i => $item): ?>
        <div class="menu-item-card" id="item-<?= $i ?>">
          <div class="card-header">
            <span class="card-num">Ret <?= $i + 1 ?></span>
            <button type="button" class="btn-remove" onclick="removeItem(this)">✕ Fjern</button>
          </div>
          <div class="field-row">
            <div class="field">
              <label>Navn på ret</label>
              <input type="text" name="item_title[]" value="<?= htmlspecialchars($item['title']) ?>" placeholder="f.eks. Flæskesteg" />
            </div>
            <div class="field triple">
              <label>Pris pr. kuvert (kr.)</label>
              <input type="number" name="item_price[]" value="<?= htmlspecialchars($item['price']) ?>" placeholder="165" />
            </div>
          </div>
          <div class="field-row single">
            <div class="field">
              <label>Beskrivelse af retten</label>
              <textarea name="item_description[]"><?= htmlspecialchars($item['description']) ?></textarea>
            </div>
          </div>
          <div class="field-row single">
            <div class="field">
              <label>Tilbehør / "hertil serveres"</label>
              <textarea name="item_sides[]"><?= htmlspecialchars($item['sides']) ?></textarea>
            </div>
          </div>
          <div class="field-row" style="grid-template-columns: 160px 1fr;">
            <div class="field">
              <label>Min. antal kuverter</label>
              <input type="number" name="item_min_covers[]" value="<?= htmlspecialchars($item['min_covers'] ?? '8') ?>" />
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <button type="button" class="btn-add" onclick="addItem()">＋ Tilføj ny ret</button>
    </div>

    <!-- Bottom save button -->
    <button type="submit" class="btn-primary" style="margin-top:8px;">💾 Gem alle ændringer</button>
  </main>
</div>
</form>

<?php if ($saveMessage): ?>
<div class="toast">✓ <?= htmlspecialchars($saveMessage) ?></div>
<?php endif; ?>

<script>
let itemCount = <?= count($menuItems) ?>;

function addItem() {
  const container = document.getElementById('menu-items-container');
  const card = document.createElement('div');
  card.className = 'menu-item-card';
  card.innerHTML = `
    <div class="card-header">
      <span class="card-num">Ny ret</span>
      <button type="button" class="btn-remove" onclick="removeItem(this)">✕ Fjern</button>
    </div>
    <div class="field-row">
      <div class="field">
        <label>Navn på ret</label>
        <input type="text" name="item_title[]" placeholder="f.eks. Lammesteg" />
      </div>
      <div class="field">
        <label>Pris pr. kuvert (kr.)</label>
        <input type="number" name="item_price[]" placeholder="165" />
      </div>
    </div>
    <div class="field-row single">
      <div class="field">
        <label>Beskrivelse af retten</label>
        <textarea name="item_description[]" placeholder="Beskriv retten..."></textarea>
      </div>
    </div>
    <div class="field-row single">
      <div class="field">
        <label>Tilbehør / "hertil serveres"</label>
        <textarea name="item_sides[]" placeholder="Hertil serveres: ..."></textarea>
      </div>
    </div>
    <div class="field-row" style="grid-template-columns: 160px 1fr;">
      <div class="field">
        <label>Min. antal kuverter</label>
        <input type="number" name="item_min_covers[]" value="8" />
      </div>
    </div>
  `;
  container.appendChild(card);
  card.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function removeItem(btn) {
  const card = btn.closest('.menu-item-card');
  if (document.querySelectorAll('.menu-item-card').length <= 1) {
    alert('Du skal have mindst én ret på menuen.');
    return;
  }
  if (confirm('Er du sikker på, at du vil fjerne denne ret?')) {
    card.style.opacity = '0';
    card.style.transform = 'scale(0.95)';
    card.style.transition = 'all 0.2s';
    setTimeout(() => card.remove(), 200);
  }
}
</script>

<?php endif; ?>
</body>
</html>
