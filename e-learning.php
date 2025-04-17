<?php
// ucat_notes.php
session_start();
include './dbconnection/connection.php';
include './php/validateSession.php';

// 1) Check subscription status
$userId     = $_SESSION['userId'] ?? null;
$subscribed = false;
if ($userId) {
    $today = date('Y-m-d');
    $stmt  = $conn->prepare("
        SELECT 1
          FROM subscription
         WHERE UserId = ?
           AND SubscriptionStatus = 1
           AND expirationDate > ?
         LIMIT 1
    ");
    $stmt->bind_param('is', $userId, $today);
    $stmt->execute();
    $subscribed = ($stmt->get_result()->num_rows > 0);
    $stmt->close();
}

// 2) UCAT notes
$notes = [
    'Verbal Reasoning' => <<<'HTML'
<section class="note-section">
  <h2 class="section-title">1. Verbal Reasoning</h2>
  <div class="intro">
    <h3 class="sub-title">What is it?</h3>
    <p>The Verbal Reasoning subtest is the first section in UCAT. It assesses your ability to quickly read a passage, locate relevant information, critically evaluate it, and make logical conclusions.</p>
  </div>
  <div class="why">
    <h3 class="sub-title">Why is it important in Medicine?</h3>
    <p>Doctors interpret information from textbooks, journals and referral letters quickly, and communicate clearly to both colleagues and patients. They also critically appraise research findings to provide the best care.</p>
  </div>
  <div class="format">
    <h3 class="sub-title">Format &amp; Timing</h3>
    <ul>
      <li>11 passages (200–400 words) × 4 questions each = 44 questions.</li>
      <li>21 minutes total – under 30 seconds per question.</li>
      <li>Question types: <strong>True</strong>, <strong>False</strong>, <strong>Can’t Tell</strong>.</li>
    </ul>
  </div>
  <div class="example">
    <h3 class="example-title">Example Question</h3>
    <div class="passage"><strong>Passage:</strong> There are several species of citrus trees whose fruits are called limes, including the Key lime (<em>Citrus aurantiifolia</em>), Persian lime (<em>Citrus latifolia</em>), kaffir lime (<em>Citrus hystrix</em>), and desert lime (<em>Citrus glauca</em>)…</div>
    <div class="question"><strong>Question:</strong> Citrus <em>latifolia</em> contains higher concentrations of furanocoumarins than <em>aurantiifolia</em>. (A) True  (B) False  (C) Can’t Tell</div>
    <div class="answer"><strong>Answer:</strong> A</div>
    <div class="solution"><strong>Solution:</strong> Persian limes are more phototoxic than Key limes, and phototoxicity is due to furanocoumarins—so A is correct.</div>
  </div>
  <div class="strategies">
    <h3 class="sub-title">Quick Strategies</h3>
    <ul>
      <li><strong>Speed‑read:</strong> Skim for main idea and signal words (<em>however</em>, <em>although</em>).</li>
      <li><strong>Stick to the text:</strong> Don’t bring in outside knowledge.</li>
      <li><strong>Process of elimination:</strong> Drop answers with absolutes (“always”, “never”) unless the passage explicitly supports them.</li>
    </ul>
  </div>
</section>
HTML
    ,
    'Decision Making' => <<<'HTML'
<section class="note-section">
  <h2 class="section-title">2. Decision Making</h2>
  <!-- Paste your full extracted Decision Making text here -->
</section>
HTML
    ,
    'Quantitative Reasoning' => <<<'HTML'
<section class="note-section">
  <h2 class="section-title">3. Quantitative Reasoning</h2>
  <!-- Paste your full extracted Quantitative Reasoning text here -->
</section>
HTML
    ,
    'Abstract Reasoning' => <<<'HTML'
<section class="note-section">
  <h2 class="section-title">4. Abstract Reasoning</h2>
  <!-- Paste your full extracted Abstract Reasoning text here -->
</section>
HTML
    ,
    'Situational Judgement' => <<<'HTML'
<section class="note-section">
  <h2 class="section-title">5. Situational Judgement</h2>
  <!-- Paste your full extracted Situational Judgement text here -->
</section>
HTML
    ,
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>UCAT Notes Dashboard</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
    rel="stylesheet">
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    :root {
      --accent: #4bc2c5;
      --bg: #fff;
      --fg: #333;
    }
    html, body {
      height: auto;
      min-height: 100vh;
      margin: 0;
      padding: 0;
      overflow: auto;
      user-select: none;
      background: #f4f4f4;
      color: var(--fg);
    }
    .tabs {
      display: flex;
      margin-bottom: 1rem;
      border-bottom: 2px solid #ccc;
    }
    .tab {
      padding: .75rem 1.5rem;
      background: #eee;
      cursor: pointer;
      transition: background .2s;
      margin-right: 2px;
    }
    .tab.active {
      background: var(--bg);
      border-top: 2px solid var(--accent);
      border-left: 2px solid var(--accent);
      border-right: 2px solid var(--accent);
      font-weight: bold;
    }
    .panel { display: none; }
    .panel.active { display: block; }
    pre, .note-section {
      max-width: 100%;
    }
    .note-section {
      background: #fff;
      padding: 1.5rem;
      margin-bottom: 2rem;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .section-title {
      color: var(--accent);
      font-size: 1.75rem;
      margin-bottom: .75rem;
      border-bottom: 3px solid var(--accent);
      padding-bottom: .5rem;
    }
    .sub-title {
      color: #ff7a7a;
      font-size: 1.25rem;
      margin-top: 1rem;
    }
    .intro, .why, .format, .strategies {
      margin-bottom: 1.25rem;
    }
    .example {
      background: #e9f9f9;
      padding: 1rem;
      border-left: 5px solid var(--accent);
      border-radius: 4px;
      margin-top: 1rem;
    }
    .example-title { color: var(--accent); font-size: 1.25rem; }
    .passage, .question, .answer, .solution {
      padding: .75rem;
      margin: .75rem 0;
      border-radius: 4px;
    }
    .passage { background: #fff8e1; }
    .question { background: #ffecb3; }
    .answer { background: #dcedc8; }
    .solution { background: #ffcdd2; }
    .locked {
      text-align: center;
      padding: 2rem;
      color: #888;
      font-size: 1.25rem;
    }
    .subscribe-btn {
      width: 100%;
      display: block;
      text-align: center;
      padding: .75rem;
      background: var(--accent);
      color: #fff;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
      margin: 1rem 0;
      transition: background .2s;
    }
    .subscribe-btn:hover { background: #369ea8; }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-md-3 col-lg-2 bg-white border-end vh-100 overflow-auto p-0">
        <?php include './partials/dashboardNavigation.php'; ?>
        <?php if (!$subscribed): ?>
          <a href="../payment/checkout.php" class="subscribe-btn">
            <i class="fas fa-lock me-1"></i> Subscribe to Unlock Notes
          </a>
        <?php endif; ?>
      </div>

      <!-- Main Content -->
      <main class="col-md-9 col-lg-10 p-4 vh-100 overflow-auto">
        <h2 class="mb-4">UCAT Full Notes</h2>
        <div class="tabs">
          <?php $i = 0; foreach ($notes as $title => $_): ?>
            <div class="tab <?= $i===0?'active':'' ?>" data-index="<?= $i++ ?>">
              <?= htmlspecialchars($title) ?>
            </div>
          <?php endforeach; ?>
        </div>

        <?php $i = 0; foreach ($notes as $title => $content): ?>
          <div class="panel <?= $i===0?'active':'' ?>" data-index="<?= $i++ ?>">
            <?php if ($subscribed): ?>
              <?= $content ?>
            <?php else: ?>
              <div class="locked">
                You must subscribe to read the full <strong><?= htmlspecialchars($title) ?></strong> notes.
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </main>
    </div>
  </div>

  <script>
    // disable text selection + context menu + copy/paste + PrintScreen
    document.addEventListener('contextmenu', e => e.preventDefault());
    ['copy','cut','paste'].forEach(evt =>
      document.addEventListener(evt, e => e.preventDefault())
    );
    document.addEventListener('keydown', e => {
      if (e.key === 'PrintScreen' || (e.ctrlKey && ['c','x','v'].includes(e.key.toLowerCase()))) {
        e.preventDefault();
        alert('Copying and screenshots are disabled.');
      }
    });

    // Tab switching
    document.querySelectorAll('.tab').forEach(tab =>
      tab.addEventListener('click', () => {
        const idx = tab.dataset.index;
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
        tab.classList.add('active');
        document.querySelector(`.panel[data-index="${idx}"]`).classList.add('active');
      })
    );
  </script>
</body>
</html>
