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
  $stmt  = $conn->prepare("SELECT 1
       FROM subscription
      WHERE UserId = ?
        AND SubscriptionStatus = 1
        AND (Item = 'instructor' OR Item = 'notes')
        AND expirationDate > ?
      LIMIT 1
    ");
  $stmt->bind_param('is', $userId, $today);
  $stmt->execute();
  $subscribed = ($stmt->get_result()->num_rows > 0);
  $stmt->close();
}

// 2) UCAT notes

if (isset($_SESSION["userId"])) {
  $userId = $_SESSION["userId"];
  $date = date("Y-m-d");
// assuming $date is something like '2025-04-17'
// $CheckIfSubscribed = mysqli_query(
//   $conn,
//   "SELECT * 
//      FROM subscription 
//     WHERE UserId = $userId 
//       AND expirationDate < '$date'"
// );
// if ($CheckIfSubscribed->num_rows > 0) {
//     $subscriptionStatus = "Subscribed";

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
        <p>Doctors need to interpret information from textbooks, journals, referral letters and other sources quickly, and communicate information clearly to other health professionals and patients. They also need to critically appraise research findings in order to provide patients with the best possible care.</p> 
      </div>
    
      <div class="format">
        <h3 class="sub-title">Format &amp; Timing</h3>
        <ul>
          <li>11 passages of text (200–400 words) × 4 questions each = 44 questions.</li>
          <li>21 minutes total – under two minutes per passage and under 30 seconds per question.</li>
          <li>Question types: <strong>True</strong>, <strong>False</strong>, <strong>Can’t Tell</strong>.</li>
        </ul> 
      </div>
    
      <div class="example">
        <h3 class="example-title">Example Question – True, False, Can’t Tell</h3>
        <div class="passage"><strong>Passage:</strong> There are several species of citrus trees whose fruits are called limes, including the Key lime (<em>Citrus aurantiifolia</em>), Persian lime (<em>Citrus latifolia</em>), kaffir lime (<em>Citrus hystrix</em>), and desert lime (<em>Citrus glauca</em>)…</div>
        <div class="question"><strong>Question:</strong> Citrus <em>latifolia</em> contains higher concentrations of furanocoumarins than <em>aurantiifolia</em>. (A) True  (B) False  (C) Can’t Tell</div>
        <div class="answer"><strong>Answer:</strong> A</div>
        <div class="solution"><strong>Solution:</strong> Persian limes are more phototoxic than Key limes, and phototoxicity is due to furanocoumarins—so A is correct.</div> 
    
        <h3 class="example-title">Example Question – Reading Comprehension</h3>
        <div class="passage"><strong>Passage:</strong> The Mechanical Turk was a fake chess-playing machine, constructed and unveiled in 1770 by Wolfgang von Kempelen to impress the Empress Maria Theresa of Austria…</div>
        <div class="question"><strong>Question:</strong> Which of the following statements cannot be inferred from the passage? (A) The Turk began its tour of Europe in April of 1783. (B) During its European tour, the Turk won almost all of its matches. (C) Philidor found his match with the Turk challenging. (D) The Turk’s visit to Vienna preceded its appearance in Paris.</div>
        <div class="solution"><strong>Solution:</strong> Options A, C, and D can be inferred from the passage, but B cannot—as the passage states the Turk often lost matches. The correct answer is B.</div> 
      </div>
    
      <div class="strategies">
        <h3 class="sub-title">Strategies for Answering Verbal Reasoning Questions</h3>
        <ul>
          <li><strong>Know Your Enemy (the Passage)</strong>
            <ul>
              <li><strong>Skim Smartly:</strong> Quickly identify the passage’s main idea, tone, and structure. Look for topic sentences or shifts in perspective.</li>
              <li><strong>Spot the Signal Words:</strong> Words like “however,” “although,” or “despite” signal changes or contradictions that are key to the correct answer.</li>
            </ul>
          </li>
          <li><strong>Answer What’s on the Page</strong>
            <ul>
              <li><strong>Stick to the Text:</strong> Don’t bring in outside knowledge; every answer must be supported by the passage.</li>
              <li><strong>Dissect the Question:</strong> Watch for qualifiers (“always,” “never”) and negatives—they’re traps meant to trick you into overthinking.</li>
            </ul>
          </li>
          <li><strong>Time is Your Worst Enemy—Manage It</strong>
            <ul>
              <li><strong>Don’t Over-Analyze:</strong> Make quick judgments and move on to avoid wasting time.</li>
              <li><strong>Practice Under Pressure:</strong> Time yourself in practice tests to build urgency and trust your instincts.</li>
            </ul>
          </li>
          <li><strong>Use the Process of Elimination</strong>
            <ul>
              <li><strong>Kill the Wrong Answers:</strong> Eliminate unsupported options to narrow your choices.</li>
              <li><strong>Beware of Extremes:</strong> Answers with absolute language are rarely correct unless explicitly supported.</li>
            </ul>
          </li>
          <li><strong>Develop a Methodical Approach</strong>
            <ul>
              <li><strong>Annotate and Note Key Points:</strong> Underline or jot down crucial ideas to navigate the passage efficiently.</li>
              <li><strong>Question Order:</strong> Answer easier questions first, then return to tougher ones to maintain momentum.</li>
            </ul>
          </li>
          <li><strong>Sharpen Your Critical Thinking</strong>
            <ul>
              <li><strong>Practice Inference:</strong> Learn to read between the lines and infer the author’s intentions.</li>
              <li><strong>Regular Review:</strong> Review mistakes after practice to understand why answers were wrong and improve.</li>
            </ul>
          </li>
          <li><strong>Trust Your Instincts (But Verify!)</strong>
            <ul>
              <li><strong>Go with Your Gut:</strong> Your first impression often is correct, but double‑check if time allows.</li>
              <li><strong>Stay Alert:</strong> Be wary of overthinking—only second‑guess if you’re sure you erred.</li>
            </ul>
          </li>
        </ul> 
      </div>
    
      <div class="closing">
        <p>END!</p> 
      </div>
    </section>
    
    HTML,
      'Decision Making' => <<<'HTML'
    <section class="note-section">
      <h2 class="section-title">2. Decision Making</h2>
    
      <div class="intro">
        <h3 class="sub-title">What is it?</h3>
        <p>Decision Making is the second subtest of the UCAT. It assesses your ability to solve problems, draw logical conclusions and evaluate arguments.</p> 
      </div>
    
      <div class="why">
        <h3 class="sub-title">Why is it important in Medicine?</h3>
        <p>Decision making is central to the role of a health professional. Doctors need to be able to solve problems, manage risk and deal with uncertainty. Health professionals are often required to make decisions quickly, in complex or stressful situations.</p> 
      </div>
    
      <div class="format">
        <h3 class="sub-title">Format &amp; Timing</h3>
        <ul>
          <li>The Decision Making subtest has 29 questions; each is individual—associated with text and/or a diagram, followed by an independent question.</li>
          <li>31 minutes total – just over one minute per question.</li>
          <li>Six major question types are included in this section.</li>
        </ul> 
      </div>
    
      <div class="example">
        <h3 class="example-title">1. Logical Puzzles</h3>
        <p>These questions present you with a puzzle or game that you need to solve in order to arrive at the answer. You are usually presented with text, with or without an accompanying diagram. Time limit per question ~60 seconds.</p> 
        <div class="passage"><strong>Stimulus:</strong> An Olympic athlete has put her medals up on the wall for everyone to admire. She has won six medals—two gold and four silver—from the 2000 and 2004 Olympics. They are arranged as shown:</div> 
        <ul>
          <li>Medal 2 is gold.</li>
          <li>Both gold medals were won in 2004.</li>
          <li>Medals 1 and 3 were won in 2000.</li>
          <li>At most three silver medals are on the corners.</li>
          <li>All medals won in 2000 are adjacent to at least two medals won in 2004.</li>
        </ul> 
        <div class="question"><strong>Question:</strong> Which of the following could be the kinds of medals that 4, 5, and 6 are, respectively?<br>(A) Gold, silver, gold. (B) Silver, gold, silver. (C) Silver, silver, gold. (D) Silver, silver, silver</div>
        <div class="solution"><strong>Answer &amp; Solution:</strong> By applying the constraints, medals 4 and 6 must be from 2004 and medal 5 must be silver. The possible order is silver, silver, gold—option C.</div> 
    
        <h3 class="example-title">2. Syllogisms</h3>
        <p>Use deductive reasoning to assess a series of conclusions. Place “Yes” if the conclusion follows; “No” if it does not.</p> 
        <div class="passage"><strong>Stimulus:</strong> At a conference for anaesthetists in Sydney last year, none were men who had subspecialised in chronic pain management.</div> 
        <p><strong>Statements:</strong><br>
          A. Only female anaesthetists were present.<br>
          B. Any man at the conference was not a chronic pain management specialist.<br>
          C. There were female anaesthetists who had subspecialised in chronic pain.<br>
          D. Very few male anaesthetists were present.<br>
          E. No anaesthetist who subspecialised in chronic pain management was a man.
        </p>
        <div class="solution"><strong>Answers:</strong> A. No. B. Yes. C. No. D. No. E. Yes.</div> 
    
        <h3 class="example-title">3. Interpreting Information</h3>
        <p>Interpret text, charts, or graphs to decide if each conclusion follows. Place “Yes” or “No.”</p> 
        <div class="passage"><strong>Stimulus:</strong> Jeremy, Tony, Jacob and Lucy are students who go to the same school. Jeremy only follows two people from school on Instagram. Everyone at school follows Lucy. The only people Tony follows are those who follow him first, and Tony follows Jeremy.</div> 
        <p><strong>Conclusions:</strong><br>
          A. Jeremy follows Tony and Lucy.<br>
          B. Lucy follows Tony.<br>
          C. Jacob follows Lucy.<br>
          D. The number who follow Tony ≥ the number Tony follows.<br>
          E. Lucy follows Jeremy.
        </p>
        <div class="solution"><strong>Answers:</strong> A. Yes. B. Yes. C. Yes. D. Yes. E. No.</div> 
    
        <h3 class="example-title">4. Recognising Assumptions</h3>
        <p>Analyse a statement and choose the strongest supporting argument.</p> 
        <div class="passage"><strong>Stimulus:</strong> Should the Australian government be able to monitor and read all telephone conversations?</div> 
        <p><strong>Options:</strong><br>
          1. Yes—important for police to prevent terrorism.<br>
          2. Yes—for infrastructure planning.<br>
          3. No—technically very difficult.<br>
          4. No—serious infringement of civil liberties.
        </p>
        <div class="solution"><strong>Solution:</strong> Option 4 is strongest; it directly addresses the question and cites civil liberties concerns.</div> 
    
        <h3 class="example-title">5. Venn Diagrams</h3>
        <p>Answer questions using Venn diagrams with overlapping sets.</p> 
        <div class="passage"><strong>Stimulus:</strong> A survey determined which forms of Asian entertainment media students engage with in their spare time.</div> 
        <p><strong>Question:</strong> Which statement is true?<br>
          (A) More students engage with C‑dramas and anime than with K‑dramas and K‑pop only.<br>
          (B) 17 students engage with anime and K‑pop but not K‑dramas.<br>
          (C) Less than 20% of K‑drama viewers also watch anime.<br>
          (D) Less than half the students engage with K‑pop.
        </p>
        <div class="solution"><strong>Solution:</strong> Option A is correct; 14 engage with C‑dramas & anime vs 8 with K‑dramas & K‑pop only. Other options are incorrect upon analysis.</div> 
    
        <h3 class="example-title">6. Probabilistic Reasoning</h3>
        <p>Use probability principles to select the best answer.</p> 
        <div class="passage"><strong>Stimulus:</strong> Joe has five 50‑cent and three 5‑cent pieces. He picks two without replacement. He states the probability both are 50‑cent is 1/4.</div> 
        <p><strong>Question:</strong> Is Joe correct?<br>
          (A) Yes—1/2 × 1/2.<br>
          (B) Yes—because random.<br>
          (C) No—5/16.<br>
          (D) No—5/14.
        </p>
        <div class="solution"><strong>Solution:</strong> Probability = (5/8)×(4/7)=20/56=5/14; Joe is incorrect—answer D.</div> 
      </div>
    
      <div class="strategies">
        <h3 class="sub-title">Strategies for Decision Making Questions</h3>
        <ol>
          <li><strong>Syllogisms</strong>
            <ul>
              <li>Diagram with Venn tools for categorical propositions.</li>
              <li>Validity checks and memorization of common valid forms.</li>
              <li>Structural analysis: identify terms and distribution.</li>
            </ul>
          </li>
          <li><strong>Logic Puzzles</strong>
            <ul>
              <li>Grid/table construction and stepwise elimination.</li>
              <li>Constraint prioritization: handle absolutes first.</li>
            </ul>
          </li>
          <li><strong>Interpreting Information</strong>
            <ul>
              <li>Active reading: highlight key data and paraphrase.</li>
              <li>Source evaluation: distinguish fact vs opinion.</li>
              <li>Pattern recognition: identify correlations or inconsistencies.</li>
            </ul>
          </li>
          <li><strong>Recognising Assumptions</strong>
            <ul>
              <li>Gap analysis: what must be true for the conclusion?</li>
              <li>Negation test: negate the assumption to test strength.</li>
              <li>Distinguish necessary vs sufficient assumptions.</li>
            </ul>
          </li>
          <li><strong>Venn Diagrams</strong>
            <ul>
              <li>Multi‑set visualization with overlapping regions.</li>
              <li>Translate syllogisms into Venn diagrams.</li>
              <li>Practice with complex overlapping categories.</li>
            </ul>
          </li>
          <li><strong>Probabilistic Reasoning</strong>
            <ul>
              <li>Master addition and multiplication rules; use tree diagrams.</li>
              <li>Apply Bayesian thinking for conditional probabilities.</li>
              <li>Avoid fallacies like base‑rate neglect and gambler’s fallacy.</li>
            </ul>
          </li>
        </ol>
        <p><strong>General Strategies:</strong> Develop systematic approaches, practice under timed conditions, and reflect on reasoning processes.</p> 
        <h3 class="sub-title">Example Applications</h3>
        <p><strong>Syllogism Example:</strong> “All dogs bark. Fido is a dog. Therefore, Fido barks.” Use a Venn diagram to confirm validity.</p> 
        <p><strong>Logic Puzzle Example:</strong> Use a grid to solve “Anna, Bob, and Carol each like a different fruit: apple, banana, cherry...”</p> 
      </div>
    
      <div class="closing">
        <p>END!</p> 
      </div>
    </section>
    HTML,
      'Quantitative Reasoning' => <<<'HTML'
    <section class="note-section">
      <h2 class="section-title">3. Quantitative Reasoning</h2>
    
      <div class="intro">
        <h3 class="sub-title">What is it?</h3>
        <p>Quantitative Reasoning is not a maths test; it is a reasoning test using mathematical skills. It assesses your numerical and problem‑solving abilities.</p> 
      </div>
    
      <div class="why">
        <h3 class="sub-title">Why is it important in Medicine?</h3>
        <p>Doctors often need to make quick calculations in day‑to‑day work—such as medication dosing and research—and use these calculations to inform decisions and solve problems.</p> 
      </div>
    
      <div class="format">
        <h3 class="sub-title">Format &amp; Timing</h3>
        <ul>
          <li>36 questions in 24 minutes (≈40 seconds per question).</li>
          <li>Most questions come in sets of four; some are standalone.</li>
          <li>Questions present text and/or tables, graphs, or diagrams; select the correct answer from five choices.</li>
        </ul> 
      </div>
    
      <div class="approach">
        <h3 class="sub-title">Approach to Quantitative Reasoning</h3>
        <ol>
          <li><strong>Master key mathematical concepts</strong>
            <ul>
              <li>Focus areas: percentages, ratios, averages, rates, profit/loss, and basic algebra.</li>
              <li>Formulas: memorize essential formulas (e.g., percentage and profit calculations).</li>
              <li>Calculator efficiency: use the calculator for complex calculations; rely on mental math for simple ones to save time.</li>
            </ul>
          </li>
        </ol> 
      </div>
    
      <div class="example">
        <h3 class="example-title">Example Question 1</h3>
        <div class="passage"><strong>Stimulus:</strong> A television streaming service changes its fees from last year to this year. The following represents the original and new fees (in $ per month) for its basic, premium, and ultimate packages, as well as the number of customers paying for each package. Note: Packages can only be switched at the month’s start.</div> 
        <div class="question"><strong>Question:</strong> The company raises prices as follows: basic +10%, premium −7%, ultimate +15%. If customer numbers remain the same, what is the percentage change in monthly income? (A) 17.6%  (B) 11.75%  (C) 21%  (D) 7.25%  (E) 8.49%</div> 
        <div class="answer"><strong>Answer:</strong> 17.6% (A)</div>
        <div class="solution"><strong>Solution:</strong>  
          Last year’s income: (7×3250)+(12×7845)+(15×5220)=195,190.  
          Next year’s prices: basic =1.1×8.50=9.35; premium =14×0.93=13.02; ultimate =18×1.15=20.70.  
          Next year’s income: (9.35×4425)+(13.02×5595)+(20.70×2250)=160,795.65.  
          Difference=34,394; percentage change=34,394/195,190×100≈17.6%. 
        </div>
    
        <h3 class="example-title">Example Question 2</h3>
        <div class="passage"><strong>Stimulus:</strong> A traffic survey shows vehicle counts by colour and type. A motor reseller preorders vehicles in the same proportions. They purchased 377 white vans/minibuses.</div> 
        <div class="question"><strong>Question:</strong> How many blue buses/coaches will they pre‑order? (A) 3  (B) 26  (C) 39  (D) 104  (E) 403</div> 
        <div class="answer"><strong>Answer:</strong> 39 (C)</div>
        <div class="solution"><strong>Solution:</strong>  
          377 white vans/minibuses × (31/29)=403 total vans/minibuses.  
          Proportion of blue buses/coaches=3/31; 403×(3/31)=39. 
        </div>
      </div>
    
      <div class="strategies">
        <h3 class="sub-title">Additional Strategies</h3>
        <ol start="2">
          <li><strong>Develop data interpretation skills</strong>
            <ul>
              <li>Quickly extract relevant data from tables, charts, and graphs.</li>
              <li>Read the question first to know what information you need.</li>
              <li>Tackle all questions linked to one dataset together to avoid re‑reading.</li>
            </ul>
          </li>
          <li><strong>Time management</strong>
            <ul>
              <li>Pace yourself—aim for ~50 seconds per question; skip difficult ones and return if time allows.</li>
              <li>Prioritize easy questions for quick points.</li>
              <li>Use timed mock tests to build speed and stamina.</li>
            </ul>
          </li>
          <li><strong>Approximation and estimation</strong>
            <ul>
              <li>Round numbers (e.g., 176→180) to simplify calculations.</li>
            </ul>
          </li>
          <li><strong>Avoid common pitfalls</strong>
            <ul>
              <li>Watch for misinterpretation traps—underline keywords like “average,” “total,” and “percentage increase.”</li>
              <li>Beware distractors—recognize partial answers.</li>
              <li>Ensure unit consistency.</li>
            </ul>
          </li>
          <li><strong>Practice and review</strong>
            <ul>
              <li>Regularly take mock exams.</li>
              <li>Review mistakes to identify and correct recurrent issues.</li>
            </ul>
          </li>
        </ol> 
      </div>
    
      <div class="closing">
        <p>END!</p> 
      </div>
    </section>
    
    HTML,
      'Abstract Reasoning' => <<<'HTML'
    <section class="note-section">
      <h2 class="section-title">4. Abstract Reasoning</h2>
    
      <div class="intro">
        <h3 class="sub-title">What is it?</h3>
        <p>Abstract Reasoning is the fourth subtest in UCAT. It assesses your non‑verbal and visuo‑spatial reasoning ability by requiring you to identify patterns, spot trends, engage in hypothesis testing, and ignore distracting information.</p> 
      </div>
    
      <div class="why">
        <h3 class="sub-title">Why is it important in Medicine?</h3>
        <p>Much of a senior health professional’s work involves pattern recognition. Doctors often need to generate and test hypotheses, extract relevant information, and identify trends in clinical practice and research.</p> 
      </div>
    
      <div class="format">
        <h3 class="sub-title">Format &amp; Timing</h3>
        <ul>
          <li>55 questions in 13 minutes—an average of less than 15 seconds per question.</li>
          <li>Most questions come in sets of five based on two ‘sets’ of images; some are standalone.</li>
          <li>You must identify logical patterns or transformations among the shapes to select the correct answer.</li>
        </ul> 
      </div>
    
      <div class="types">
        <h3 class="sub-title">Question Types</h3>
    
        <h4>Type 1: Set A / B / Neither</h4>
        <p>You are shown two sets of shapes (Set A and Set B). A series of five test shapes follows, and you decide whether each belongs to Set A, Set B, or Neither, based on the underlying patterns in each set.</p> 
    
        <h4>Type 2: Complete the Series</h4>
        <p>A sequence of images is presented; you must extrapolate the pattern to choose the next image.</p> 
        <div class="example">
          <h5 class="example-title">Example & Solution</h5>
          <p><strong>Answer:</strong> C</p>
          <p><strong>Solution:</strong> The black circle moves two points clockwise and changes colour each move—so in the next image it is black at the top right. The square moves one point anticlockwise—so it appears bottom left. Only option C fits both rules.</p> 
        </div>
    
        <h4>Type 3: Complete the Statement (“This is to That”)</h4>
        <p>An initial image transforms into a second; you apply the same transformation to a third image to find the answer.</p> 
        <div class="example">
          <h5 class="example-title">Example & Solution</h5>
          <p><strong>Answer:</strong> A</p>
          <p><strong>Solution:</strong> In the example, the outer shape flips horizontally, changes from black to striped, and moves inside the small square; the inner triangles move outside and rotate clockwise. Applying this to the question image yields option A.</p> 
        </div>
    
        <h4>Type 4: Set A or B</h4>
        <p>Similar to Type 1, but you are given four test shapes at once and decide which single shape belongs to Set A or Set B.</p> 
        <div class="example">
          <h5 class="example-title">Example & Solution</h5>
          <p><strong>Answer:</strong> B</p>
          <p><strong>Solution:</strong> Set A has an odd number of vertically striped arrows; Set B has an even number of horizontally striped arrows in top‑right and bottom‑left positions. Only option B obeys Set A’s rules.</p> 
        </div>
      </div>
    
      <div class="strategies">
        <h3 class="sub-title">Strategies to Crack Abstract Reasoning</h3>
        <ol>
          <li><strong>Identify the Common Thread</strong>
            <ul>
              <li>Spot similarities within each set (shape, rotation, colour, arrangement).</li>
              <li>Note differences between Set A and Set B (line thickness, extra elements).</li>
            </ul>
          </li>
          <li><strong>Break Down the Transformation</strong>
            <ul>
              <li><strong>Rotation &amp; Reflection:</strong> Visualize flips and rotations.</li>
              <li><strong>Scaling &amp; Positioning:</strong> Track size changes and movement.</li>
              <li><strong>Overlay &amp; Combination:</strong> Look for added or removed elements.</li>
            </ul>
          </li>
          <li><strong>Develop a Systematic Approach</strong>
            <ul>
              <li>First glance, then analyze: scan all examples before test shapes.</li>
              <li>Create a checklist of features (Shapes, Position, Angles, Route, Touching sides, Area, Number of items).</li>
            </ul>
          </li>
          <li><strong>Practice with Timed Drills</strong>
            <ul>
              <li>Simulate the 15‑second per question pace.</li>
              <li>Mark and return to difficult items to preserve overall timing.</li>
            </ul>
          </li>
          <li><strong>Train Your Visual Memory</strong>
            <ul>
              <li>Use pattern‑spotting puzzles to sharpen recognition.</li>
              <li>Practice mental rotation exercises to handle transformations.</li>
            </ul>
          </li>
          <li><strong>Develop “Shortcut” Strategies</strong>
            <ul>
              <li>Use process of elimination to discard non‑matching options.</li>
              <li>Make educated guesses when narrowed down but uncertain.</li>
              <li>Perform a consistency check: ensure the rule applies to all examples.</li>
            </ul>
          </li>
          <li><strong>Reflect on Your Mistakes</strong>
            <ul>
              <li>Review incorrect items to understand why your rule failed.</li>
            </ul>
          </li>
        </ol>
      </div>
    
      <div class="practical-tips">
        <h3 class="sub-title">Practical Abstract Reasoning Tips</h3>
        <ul>
          <li>If you assign to Set A, it must fit every example in Set A; likewise for Set B.</li>
          <li>Start with a small “simplex” subset of the set to identify core rules before generalizing.</li>
          <li>Ignore the test shape at first—identify the set’s logic, then match the test shape.</li>
          <li>Use the SPARTAN mnemonic: Shapes, Position, Angles, Route, Touching, Area, Number.</li>
          <li>If a question takes more than one minute, flag it and move on.</li>
        </ul> 
      </div>
    
      <div class="closing">
        <p>END!</p> 
      </div>
    </section>
    
    HTML,
      'Situational Judgement' => <<<'HTML'
    <section class="note-section">
      <h2 class="section-title">5. Situational Judgement Reasoning</h2>
    
      <div class="intro">
        <h3 class="sub-title">Introduction</h3>
        <ul>
          <li>SJR assesses the ability of the student to make ethical, patient‑centred decisions under pressure.</li>
          <li>Objective: After this teaching, the student should be able to analyse medical/ethical scenarios, prioritizing actions, and select appropriate responses.</li>
        </ul> 
      </div>
    
      <div class="why">
        <h3 class="sub-title">What is it?</h3>
        <p>Situational Judgement is the fifth and final subtest in UCAT. It assesses your ability to understand real‑world scenarios and identify important factors and appropriate responses. Scenarios are usually based in a university or health‑related setting, featuring a medical or dental student or junior health professional.</p> 
    
        <h3 class="sub-title">Why is it important in Medicine?</h3>
        <p>Situational Judgement tests evaluate professionalism, assessing attributes vital in medicine: empathy, adaptability, resilience, teamwork, and integrity.</p> 
      </div>
    
      <div class="format">
        <h3 class="sub-title">Format &amp; Timing</h3>
        <ul>
          <li>69 questions across 22 scenarios.</li>
          <li>26 minutes total (≈22 seconds per question).</li>
          <li>Full marks for the correct answer; partial marks for close responses.</li>
        </ul> 
      </div>
    
      <div class="example">
        <h3 class="example-title">Example Scenario 1</h3>
        <div class="passage"><strong>Passage:</strong> Brian, a junior doctor, shares a busy surgical ward with colleague John, who’s been late for the past fortnight, causing Brian to cover extra duties.</div>
        <div class="question"><strong>Question:</strong> How important is it to consider that Brian and John do not know each other well?<br>A) Very important  B) Important  C) Of minor importance  D) Not important at all</div>
        <div class="solution"><strong>Solution:</strong> D – Not important. Brian must act to protect patient care and his well‑being regardless of their relationship. He should discuss his concerns, ask why John is late, and explain the impact.</div>
      </div>
    
      <div class="example">
        <h3 class="example-title">Example Scenario 2</h3>
        <div class="passage"><strong>Passage:</strong> George, a dental student, hasn’t kept up his supervised learning events or completed agreed learning targets.</div>
        <div class="question"><strong>Question:</strong> Stay back late after placement to complete supervised learning events?<br>A) Very appropriate  B) Appropriate but not ideal  C) Inappropriate but not awful  D) Very inappropriate</div>
        <div class="solution"><strong>Solution:</strong> B – Appropriate but not ideal. It shows dedication but is unsustainable; portfolios should be updated throughout the year.</div>
      </div>
    
      <div class="example">
        <h3 class="example-title">Example Scenario 3</h3>
        <div class="passage"><strong>Passage:</strong> Joanne, a medical student, interviews Mr Jones, on home oxygen. Notes say he quit smoking, but she sees cigarettes in his pocket.</div>
        <div class="question"><strong>Question:</strong> Select the most and least appropriate actions:<br>1. Inform the senior doctor immediately.<br>2. Overlook the cigarettes to preserve rapport.<br>3. Ask Mr Jones further questions to verify.</div>
        <div class="solution"><strong>Solution:</strong> Most appropriate: 3; Least appropriate: 2. Confirm facts before action, then address patient safety.</div>
      </div> 
    
      <div class="strategies">
        <h3 class="sub-title">Best Approach Toward SJRs</h3>
        <p>To excel in Situational Judgement, understand core principles of medical professionalism: honesty, integrity, compassionate patient‑centred care, teamwork, autonomy, confidentiality, and commitment to safety and improvement.</p>
        <ol>
          <li><strong>Master the Core Competencies</strong>
            <ul>
              <li>Professionalism (integrity, accountability)</li>
              <li>Empathy (patient‑centred care)</li>
              <li>Teamwork (conflict resolution)</li>
              <li>Prioritization (urgent vs. important)</li>
              <li>Resilience (handling stress)</li>
              <li>Ethical decision‑making (confidentiality, fairness)</li>
            </ul>
          </li>
          <li><strong>Use the “PRICE” Framework</strong>
            <ul>
              <li>Problem: Identify the core issue.</li>
              <li>Relevant factors: Stakeholders, rules, consequences.</li>
              <li>Ideas: Generate options.</li>
              <li>Choose: Select best and second‑best actions.</li>
              <li>Evaluate: Align with values and long‑term outcomes.</li>
            </ul>
          </li>
          <li><strong>Prioritize Patient/Client Welfare</strong>
            <p>Safety and ethics override convenience. E.g., report an error to prevent harm.</p>
          </li>
          <li><strong>Avoid Extreme Responses</strong>
            <p>Balance is key: address issues privately, then escalate if needed.</p>
          </li>
          <li><strong>Think Long‑Term Consequences</strong>
            <p>Consider trust, policy compliance, and sustainability of actions.</p>
          </li>
          <li><strong>Rank Order Tactics</strong>
            <p>Eliminate unethical options first, prioritize root‑cause solutions, follow escalation protocols.</p>
          </li>
          <li><strong>Practice “What Would a Role Model Do?”</strong>
            <p>Imagine an ideal professional’s actions to guide your choice.</p>
          </li>
          <li><strong>Watch for Keywords</strong>
            <p>“Immediately” signals urgency; “Discuss first,” collaboration; “Confidentiality,” data protection.</p>
          </li>
          <li><strong>Common Pitfalls</strong>
            <p>Avoid policy breaches, personal bias, and misjudging urgency.</p>
          </li>
          <li><strong>Simulate Real Scenarios</strong>
            <p>Use past papers, review rationales, and role‑play with peers.</p>
          </li>
        </ol>
      </div>
    
      <div class="practical-tips">
        <h3 class="sub-title">4 Core Principles of SJR</h3>
        <ul>
          <li>Patient safety first: act immediately if health is at risk.</li>
          <li>Teamwork: resolve conflicts privately without blame.</li>
          <li>Follow rules with kindness: maintain respect even if policies are broken.</li>
          <li>Ask for help when stuck: consult a senior rather than guess.</li>
        </ul> 
      </div>
    
      <div class="closing">
        <p>END!</p> 
      </div>
    </section>
    HTML,
    ];
//   } else {
//     $subscriptionStatus = "Unsubscribed";
//     $notes = [
//       'Verbal Reasoning' => <<<'HTML'
//     <section class="note-section"><h2 class="section-title">Subcribe to Access notes</h2></section>
    
//     HTML,
//       'Decision Making' => <<<'HTML'
//     <section class="note-section"><h2 class="section-title">Subcribe to Access notes</h2></section></section>
    
//     HTML,
//       'Quantitative Reasoning' => <<<'HTML'
//     <section class="note-section"><h2 class="section-title">Subcribe to Access notes</h2></section></section>
    
    
//     HTML,
//       'Abstract Reasoning' => <<<'HTML'
//     <section class="note-section"><h2 class="section-title">Subcribe to Access notes</h2></section></section>
    
    
//     HTML,
//       'Situational Judgement' => <<<'HTML'
//     <section class="note-section"><h2 class="section-title">Subcribe to Access notes</h2></section></section>
//     HTML,
//     ];
//   }
}

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
    <link rel="shortcut icon" href="https://mkscholars.com/images/logo/logoRound.png" type="image/x-icon">
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    :root {
      --accent: #4bc2c5;
      --bg: #fff;
      --fg: #333;
    }

    html,
    body {
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

    .panel {
      display: none;
    }

    .panel.active {
      display: block;
    }

    pre,
    .note-section {
      max-width: 100%;
    }

    .note-section {
      background: #fff;
      padding: 1.5rem;
      margin-bottom: 2rem;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
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

    .intro,
    .why,
    .format,
    .strategies {
      margin-bottom: 1.25rem;
    }

    .example {
      background: #e9f9f9;
      padding: 1rem;
      border-left: 5px solid var(--accent);
      border-radius: 4px;
      margin-top: 1rem;
    }

    .example-title {
      color: var(--accent);
      font-size: 1.25rem;
    }

    .passage,
    .question,
    .answer,
    .solution {
      padding: .75rem;
      margin: .75rem 0;
      border-radius: 4px;
    }

    .passage {
      background: #fff8e1;
    }

    .question {
      background: #ffecb3;
    }

    .answer {
      background: #dcedc8;
    }

    .solution {
      background: #ffcdd2;
    }

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

    .subscribe-btn:hover {
      background: #369ea8;
    }
  </style>
</head>

<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-md-3 col-lg-2 bg-white border-end vh-100 overflow-auto p-0">
        <?php include './partials/dashboardNavigation.php'; ?>

      </div>

      <!-- Main Content -->
      <main class="col-md-9 col-lg-10 p-4 vh-100 overflow-auto">
        <h2 class="mb-4">UCAT Full Notes</h2>
        <div class="tabs">
          <?php $i = 0;
          foreach ($notes as $title => $_): ?>
            <div class="tab <?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i++ ?>">
              <?= htmlspecialchars($title) ?>
            </div>
          <?php endforeach; ?>
        </div>
        <div>
          <?php
          if ($subscriptionStatus == "Subscribed") {
          ?>
          <?php
          }
          ?>
        </div>
        <?php $i = 0;
        foreach ($notes as $title => $content): ?>
          <div class="panel <?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i++ ?>">
            <?php if ($subscribed): ?>
              <?= $content ?>
            <?php else: ?>
              <div class="locked">
                You must Pay to read the full <strong><?= htmlspecialchars($title) ?></strong> notes.
                <?php if (!$subscribed): ?>
                  <a href="./ucat" class="subscribe-btn">
                    <i class="fas fa-lock me-1"></i> Pay now to get Full Access
                  </a>
                <?php endif; ?>

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
    ['copy', 'cut', 'paste'].forEach(evt =>
      document.addEventListener(evt, e => e.preventDefault())
    );
    document.addEventListener('keydown', e => {
      if (e.key === 'PrintScreen' || (e.ctrlKey && ['c', 'x', 'v'].includes(e.key.toLowerCase()))) {
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