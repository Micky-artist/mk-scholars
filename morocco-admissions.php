<?php
session_start();
include './dbconnection/connection.php';
include './php/validateSession.php';

$userId = $_SESSION['userId'] ?? null;
$subscribed = false;
if ($userId) {
  $today = date('Y-m-d');
  $stmt = $conn->prepare(
    "SELECT 1 FROM subscription
     WHERE UserId = ?
       AND SubscriptionStatus = 1
       AND Item = 'moroccoadmissions'
       AND expirationDate > ?
     LIMIT 1"
  );
  $stmt->bind_param('is', $userId, $today);
  $stmt->execute();
  $subscribed = ($stmt->get_result()->num_rows > 0);
  $stmt->close();
}
$sections = [
  [
    'tab'    => 'Interview Questions',
    'title'  => 'Interview Questions',
    'items'  => [
      ['q' => '1. Why are you interested in pursuing a degree in hospitality business and management?',
       'options' => [
         'A) To gain management skills in hospitality.',
         'B) To enter the finance industry.',
         'C) To work in information technology.',
         'D) To study sustainable agriculture.'
       ],
       'answer' => 'A) To gain management skills in hospitality.'
      ],
      ['q' => '2. What do you know about the hospitality industry, and why do you want to be a part of it?',
       'options' => [
         'A) It focuses on manufacturing.',
         'B) It emphasizes guest experience and service.',
         'C) It is unrelated to customer interaction.',
         'D) It is exclusively about travel.'
       ],
       'answer' => 'B) It emphasizes guest experience and service.'
      ],
      ['q' => '3. Can you describe any relevant experience you have in the hospitality or service industry?',
       'options' => [
         'A) Internship at a hotel front desk.',
         'B) Volunteer in a software firm.',
         'C) Research assistant in a lab.',
         'D) Sales at an electronics store.'
       ],
       'answer' => 'A) Internship at a hotel front desk.'
      ],
      ['q' => '4. How do you handle stressful situations or difficult customers?',
       'options' => [
         'A) Ignore their complaints.',
         'B) Listen actively and solve issues calmly.',
         'C) Escalate immediately without empathy.',
         'D) Offer unrelated discounts.'
       ],
       'answer' => 'B) Listen actively and solve issues calmly.'
      ],
      ['q' => '5. What are your career goals, and how do you see this program helping you achieve them?',
       'options' => [
         'A) Become a software developer.',
         'B) Lead hospitality operations using program skills.',
         'C) Pursue medicine.',
         'D) Write academic papers only.'
       ],
       'answer' => 'B) Lead hospitality operations using program skills.'
      ],
      ['q' => '6. How do you think sustainability and innovation can impact the hospitality industry?',
       'options' => [
         'A) Increase environmental costs.',
         'B) Drive guest satisfaction and efficiency.',
         'C) Hinder service quality.',
         'D) Reduce operational transparency.'
       ],
       'answer' => 'B) Drive guest satisfaction and efficiency.'
      ],
      ['q' => '7. Can you provide an example of a time when you worked effectively as part of a team?',
       'options' => [
         'A) Coordinated a campus event smoothly.',
         'B) Worked alone on projects.',
         'C) Ignored team feedback.',
         'D) Handled only personal tasks.'
       ],
       'answer' => 'A) Coordinated a campus event smoothly.'
      ],
      ['q' => '8. What do you know about Mohammed VI Polytechnic University and its hospitality program?',
       'options' => [
         'A) It has no industry ties.',
         'B) It emphasizes sustainability and innovation.',
         'C) It only offers online courses.',
         'D) It is unrelated to hospitality.'
       ],
       'answer' => 'B) It emphasizes sustainability and innovation.'
      ],
      ['q' => '9. How do you plan to contribute to the UM6P community and the hospitality industry as a whole?',
       'options' => [
         'A) By isolating myself.',
         'B) By leading sustainable service projects.',
         'C) By ignoring community events.',
         'D) By focusing solely on grades.'
       ],
       'answer' => 'B) By leading sustainable service projects.'
      ],
      ['q' => '10. Why is asking questions about the program important?',
       'options' => [
         'A) To demonstrate interest and curiosity.',
         'B) To show indifference.',
         'C) To delay the interview.',
         'D) To criticize without basis.'
       ],
       'answer' => 'A) To demonstrate interest and curiosity.'
      ],
    ]
  ],
  [
    'tab'    => 'Hospitality & Tourism',
    'title'  => 'Hospitality & Tourism',
    'items'  => [
      ['q' => 'Best Hotel in Africa (Conde Nast Traveler)?',
       'options' => ['A) La Mamounia','B) Selman Marrakech','C) The Oberoi Marrakech','D) Royal Mansour Marrakech'],
       'answer' => 'D) Royal Mansour Marrakech'
      ],
      ['q' => 'Ministry of Tourism in Morocco?',
       'options' => ['A) Ministry of Hospitality and Tourism','B) Ministry of Tourism and Sustainable Development','C) Ministry of Handicraft, Social and Solidarity Economy','D) Ministry of Tourism and Culture'],
       'answer' => 'B) Ministry of Tourism and Sustainable Development'
      ],
      ['q' => '"Yield management" refers to:',
       'options' => ['A) Implementing sustainable practices','B) Maximizing revenue by adjusting room rates based on demand','C) Ensuring guest safety','D) Staff scheduling only'],
       'answer' => 'B) Maximizing revenue by adjusting room rates based on demand'
      ],
      ['q' => 'Acronym "F&B" stands for:',
       'options' => ['A) Front Desk & Bell','B) Food & Beverages','C) Facilities & Booking','D) Finance & Budget'],
       'answer' => 'B) Food & Beverages'
      ],
      ['q' => 'Most visited tourist destination (2022)?',
       'options' => ['A) Spain','B) United States','C) France','D) Italy'],
       'answer' => 'C) France'
      ],
      ['q' => 'Mandatory internships in SHBM program?',
       'options' => ['A) 1','B) 2','C) 3','D) 4'],
       'answer' => 'B) 2'
      ],
      ['q' => 'Language of teaching for SHBM?',
       'options' => ['A) Arabic','B) English','C) French','D) Spanish'],
       'answer' => 'B) English'
      ],
      ['q' => 'Current Moroccan Minister of Tourism?',
       'options' => ['A) Fatima Ezzahra El Mansouri','B) Chakib Bemroussa','C) Fatima-Zahra Ammor','D) Nadia Fettah Alaoui'],
       'answer' => 'C) Fatima-Zahra Ammor'
      ],
      ['q' => '"Dad will call you ______ he gets home."',
       'options' => ['A) after that','B) as soon as','C) before','D) until'],
       'answer' => 'B) as soon as'
      ],
      ['q' => 'Break room is upstairs with a drinks machine:',
       'options' => ['A) The drinks are free.','B) You can take a break upstairs.','C) You must bring your drink.','D) It is closed after 5pm.'],
       'answer' => 'B) You can take a break upstairs.'
      ],
      ['q' => '"This test is ______ than I expected."',
       'options' => ['A) more easy','B) easier','C) the easiest','D) easy'],
       'answer' => 'B) easier'
      ],
      ['q' => '"I ______ at home while my parents were visiting..."',
       'options' => ['A) was stayed','B) stayed','C) stay','D) have stayed'],
       'answer' => 'B) stayed'
      ],
      ['q' => '"There is ______ oil in that can."',
       'options' => ['A) many','B) a lot of','C) some','D) few'],
       'answer' => 'C) some'
      ],
      ['q' => '"We ______."',
       'options' => ['A) yesterday went to the bank','B) went to the bank yesterday','C) have went','D) go yesterday'],
       'answer' => 'B) went to the bank yesterday'
      ],
      ['q' => 'Dialogue: "What do you want for dinner?"',
       'options' => ['A) I’m not sure.','B) They’re out of touch.','C) It’s raining.','D) No idea.'],
       'answer' => 'A) I’m not sure.'
      ],
      ['q' => '"Timothy left his house ten minutes ______."',
       'options' => ['A) before','B) ago','C) later','D) earlier'],
       'answer' => 'B) ago'
      ],
      ['q' => '"Peter enjoys ______ to wake up early."',
       'options' => ['A) doesn’t have','B) not having','C) have','D) had'],
       'answer' => 'B) not having'
      ],
      ['q' => '"Tom is ______ teacher there."',
       'options' => ['A) the most nice','B) the nicest','C) more nice','D) nice'],
       'answer' => 'B) the nicest'
      ],
      ['q' => 'Areas to improve:',
       'options' => ['A) Time management','B) Public speaking','C) Technical skills','D) All of the above'],
       'answer' => 'D) All of the above'
      ],
      ['q' => 'Target position after graduation:',
       'options' => ['A) Hotel Operations Manager','B) IT Consultant','C) Research Scientist','D) HR Specialist'],
       'answer' => 'A) Hotel Operations Manager'
      ],
      ['q' => 'Strengths:',
       'options' => ['A) Teamwork','B) Adaptability','C) Multilingual','D) All of the above'],
       'answer' => 'D) All of the above'
      ],
      ['q' => 'Interest in hospitality (50 words)',
       'options' => ['A) Example provided'],
       'answer' => 'A) Example provided'
      ],
    ]
  ],
  [
    'tab'    => 'Logic Test',
    'title'  => 'Section 2: Logic Test Questions',
    'items'  => [
      ['q' => '1. In a group of 100 people, 60% like coffee, 40% like tea, and 20% like both. How many people do not like either?',
       'options' => ['A) 20','B) 30','C) 40','D) 25'],
       'answer'  => 'A) 20'
      ],
      ['q' => '2. What number is missing from this sequence: 1, 2, 4, 7, 11, 16, …?',
       'options' => ['A) 26','B) 22','C) 28','D) 18'],
       'answer'  => 'B) 22'
      ],
      ['q' => '3. Pyramid puzzle—numbers X, Y, Z?',
       'options' => ['A) X=128,Y=5,Z=9','B) X=113,Y=7,Z=7','C) X=103,Y=2,Z=3','D) X=126,Y=9,Z=12'],
       'answer'  => 'B) X=113,Y=7,Z=7'
      ],
      ['q' => '4. Saida has 4 sisters and 3 brothers. Abdelaziz is one brother. Subtract brothers from sisters.',
       'options' => ['A) 4','B) 1','C) 5','D) 3'],
       'answer'  => 'D) 3'
      ],
      ['q' => '5. Which shield should replace the question mark?',
       'options' => ['A) Option A','B) Option B','C) Option C','D) Option D'],
       'answer'  => 'C) Option C'
      ],
      ['q' => '6. Sequence missing: 3,8,15,24,35,...?',
       'options' => ['A) 40','B) 45','C) 48','D) 49'],
       'answer'  => 'C) 48'
      ],
      ['q' => '7. Word puzzle: tray oyster warnings ?',
       'options' => ['A) umbrellas','B) character','C) strawberry','D) platter'],
       'answer'  => 'D) platter'
      ],
      ['q' => '8. 72 cracked eggs =12%. Total?',
       'options' => ['A) 600','B) 864','C) 721','D) 121'],
       'answer'  => 'A) 600'
      ],
      ['q' => '9. Sequence: 3,6,18,72,_?',
       'options' => ['A) 144','B) 214','C) 272','D) 360'],
       'answer'  => 'D) 360'
      ],
      ['q' => '10. Two trains 50km/h &60km/h, 200km apart. Time to collide?',
       'options'=>['A) 1h 53min','B) 1h 45min','C) 1h 42min','D) 1h 49min'],
       'answer' => 'D) 1h 49min'
      ],
      ['q'=>'11. Letter puzzle relative to E?',
       'options'=>['A) B','B) G','C) D','D) H'],
       'answer'=>'D) H'
      ],
      ['q'=>'12. Smallest number divisible by 1–6?','options'=>['A) 60','B) 42','C) 30','D) 90'],'answer'=>'A) 60'],
      ['q'=>'13. Missing in 2,5,11,23,__,95?','options'=>['A) 35','B) 41','C) 47','D) 53'],'answer'=>'C) 47'],
      ['q'=>'14. Odd one out: 3,5,7,11,15?','options'=>['A) 3','B) 11','C) 5','D) 15'],'answer'=>'D) 15'],
      ['q'=>'15. Replace letters: A Z C X E V G ? ?','options'=>['A) DZ','B) TI','C) MU','D) PY'],'answer'=>'D) PY'],
      ['q'=>'16. Typists/pages/minutes?','options'=>['A) 9','B) 6','C) 4','D) 4.5'],'answer'=>'B) 6'],
      ['q'=>'17. Lily pad doubling: full=48 days, half?',
       'options'=>['A) 47 days','B) 24 days','C) 24.5 days','D) 40 days'],'answer'=>'A) 47 days'],
      ['q'=>'18. 27-cube exposed faces?',
       'options'=>['A) 1','B) 2','C) 3','D) 26'],'answer'=>'D) 26'],
      ['q'=>'19. Not prime: 2,23,29,33?','options'=>['A) 2','B) 23','C) 29','D) 33'],'answer'=>'D) 33'],
      ['q'=>'20. Candies: Alf/Jim/Sid=192. Alf has?','options'=>['A) 152','B)126','C)144','D)134'],'answer'=>'C) 144'],
      ['q'=>'21. Steps puzzle: 896 + half steps?',
       'options'=>['A) 1344','B)2688','C)1800','D)1792'],'answer'=>'D) 1792'],
      ['q'=>'22. Money share: 2/5,0.55,$45. Total?',
       'options'=>['A)125','B)450','C)950','D)900'],'answer'=>'D) 900'],
      ['q'=>'23. Clock loses 6min/hr. Shows correct Mon noon. Shows Thu noon?',
       'options'=>['A)11:48 AM','B)4:48 AM','C)12:06 PM','D)12:18 PM'],'answer'=>'B) 4:48 AM'],
      ['q'=>'24. Jasmine and Jill ages puzzle?','options'=>['A)J10,Jill5','B)J20,Jill25','C)J10,Jill12.5','D)J5,Jill12.5'],'answer'=>'A) Jasmine10,Jill5'],
      ['q'=>'25. Sequence 3,8,13,18,23,...?','options'=>['A)28','B)33','C)38','D)27'],'answer'=>'A) 28'],
    ],
  ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Quizzes Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root { --accent: #4bc2c5; --bg: #fff; --fg: #333; }

    body {
      background: #f4f4f4;
      color: var(--fg);
      margin: 0;
    }

    .tabs {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
      border-bottom: 2px solid #ccc;
      margin-bottom: 1rem;
      overflow-x: auto;
    }

    .tab {
      padding: .75rem 1.5rem;
      background: #eee;
      cursor: pointer;
      margin-right: 2px;
      white-space: nowrap;
      flex: 0 0 auto;
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

    .note-section {
      background: var(--bg);
      padding: 1.5rem;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .section-title {
      color: var(--accent);
      border-bottom: 3px solid var(--accent);
      padding-bottom: .5rem;
      margin-bottom: 1rem;
    }

    .question { margin-top: 1rem; font-weight: bold; }
    .options { list-style: none; padding-left: 0; }
    .options li { margin: .25rem 0; }

    .answer {
      margin-top: .5rem;
      background: #dcedc8;
      padding: .75rem;
      border-radius: 4px;
    }

    .locked {
      text-align: center;
      padding: 2rem;
      color: #888;
    }

    .subscribe-link {
      color: var(--accent);
      text-decoration: none;
      font-weight: bold;
    }

    .full-height {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    main {
      flex: 1;
      padding: 1.5rem;
    }

    #topBtn {
      position: fixed;
      bottom: 25px;
      right: 25px;
      z-index: 999;
      background: var(--accent);
      color: white;
      border: none;
      border-radius: 50%;
      width: 45px;
      height: 45px;
      font-size: 20px;
      display: none;
    }

    .btn-accent {
      background: var(--accent);
      color: white;
    }

    .btn-accent:hover {
      background: #3aa9ac;
    }

    @media (max-width: 768px) {
      .row.flex-nowrap {
        flex-direction: column !important;
      }
      nav {
        width: 100% !important;
        display: none;
      }
      nav.active {
        display: block !important;
      }
      main {
        width: 100% !important;
      }
    }
  </style>
</head>
<body>
  <div class="container-fluid full-height">
    <div class="row flex-nowrap">
      <nav class="col-md-3 col-lg-2 bg-white border-end p-0" id="sidebar">
        <?php include './partials/dashboardNavigation.php'; ?>
      </nav>

      <main class="col-md-9 col-lg-10 position-relative" id="mainContent">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <button class="btn btn-outline-secondary" onclick="window.history.back()">
            ← Go Back
          </button>
          <button class="btn btn-outline-info d-md-none" id="sidebarToggle">
            ☰ Menu
          </button>
        </div>

        <?php if ($subscribed): ?>
          <h2 class="mb-4">Quizzes Dashboard</h2>
          <div class="tabs">
            <?php foreach ($sections as $i => $sec): ?>
              <div class="tab <?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i ?>">
                <?= htmlspecialchars($sec['tab']) ?>
              </div>
            <?php endforeach; ?>
          </div>

          <?php foreach ($sections as $i => $sec): ?>
            <div class="panel <?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i ?>">
              <section class="note-section">
                <h2 class="section-title"><?= htmlspecialchars($sec['title']) ?></h2>
                <?php foreach ($sec['items'] as $item): ?>
                  <div class="question"><?= htmlspecialchars($item['q']) ?></div>
                  <ul class="options">
                    <?php foreach ($item['options'] as $opt): ?>
                      <li><?= htmlspecialchars($opt) ?></li>
                    <?php endforeach; ?>
                  </ul>
                  <div class="answer">Correct Answer: <?= htmlspecialchars($item['answer']) ?></div>
                <?php endforeach; ?>
              </section>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="locked">
            You must subscribe to access the quizzes.<br>
            <a target="_blank" href="./payment/checkout?subscription=moroccoadmissions" class="subscribe-link">Click here to subscribe</a>
          </div>
        <?php endif; ?>

        <button onclick="scrollToTop()" id="topBtn" title="Back to top" class="btn btn-accent shadow-sm">
          ↑
        </button>
      </main>
    </div>
  </div>

  <script>
    document.querySelectorAll('.tab').forEach(tab => {
      tab.addEventListener('click', () => {
        const idx = tab.dataset.index;
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
        tab.classList.add('active');
        document.querySelector(`.panel[data-index="${idx}"]`).classList.add('active');
      });
    });

    const topBtn = document.getElementById("topBtn");
    window.onscroll = () => {
      topBtn.style.display = (document.documentElement.scrollTop > 200) ? "block" : "none";
    };
    function scrollToTop() {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    sidebarToggle.addEventListener('click', () => {
      sidebar.classList.toggle('active');
    });

    // Close sidebar when clicking outside
    document.addEventListener('click', function (e) {
      if (window.innerWidth < 768 && !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
        sidebar.classList.remove('active');
      }
    });
  </script>
</body>
</html>
