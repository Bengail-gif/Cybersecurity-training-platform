/* =====================================================
   CYBERSECURITY AWARENESS TRAINING PLATFORM
   Faith University — Complete JavaScript (PHP-backed)
   ===================================================== */

// ===== 1. GLOBAL STATE =====
const API_BASE = 'api/';
let currentUser = null;
let completedModules = [];
let quizScores = [];
let currentQuizAnswers = {};

// ===== 2. MODULE CONTENT DATABASE =====
const modules = {
  passwords: {
    title: "Password Security",
    content: `
      <h3>Why Password Security Matters</h3>
      <p>Weak passwords are the #1 cause of data breaches. In 2023, over 80% of hacking-related breaches involved compromised passwords.</p>
      <h3>Creating Strong Passwords</h3>
      <ul>
        <li>Use at least 12 characters</li>
        <li>Mix uppercase, lowercase, numbers, and symbols</li>
        <li>Avoid dictionary words, names, or birthdays</li>
        <li>Never reuse passwords across sites</li>
      </ul>
      <h3>Example: Weak vs Strong</h3>
      <p><strong>Weak:</strong> password123 (cracked in 0.002 seconds)</p>
      <p><strong>Strong:</strong> K9#mP$vL2@nQ (would take centuries to crack)</p>
      <h3>Use a Password Manager</h3>
      <p>Password managers like Bitwarden, 1Password, or KeePass generate and store unique passwords for every site. You only need to remember one master password.</p>
      <h3>Enable Two-Factor Authentication (2FA)</h3>
      <p>2FA adds a second layer of security. Even if someone steals your password, they can't access your account without your phone or authenticator app.</p>
    `
  },
  phishing: {
    title: "Phishing Awareness",
    content: `
      <h3>What is Phishing?</h3>
      <p>Phishing is a cyberattack where attackers impersonate trusted entities (banks, universities, companies) to trick you into revealing sensitive information like passwords or credit card numbers.</p>
      <h3>Common Phishing Tactics</h3>
      <ul>
        <li><strong>Urgency:</strong> "Your account will be closed in 24 hours!"</li>
        <li><strong>Fear:</strong> "Unauthorized login detected!"</li>
        <li><strong>Curiosity:</strong> "You won't believe what I found..."</li>
        <li><strong>Authority:</strong> Fake emails from IT department or university admin</li>
      </ul>
      <h3>How to Spot Phishing Emails</h3>
      <ol>
        <li>Check the sender's email address (not just the display name)</li>
        <li>Look for spelling/grammar mistakes</li>
        <li>Hover over links before clicking (check the actual URL)</li>
        <li>Be suspicious of unexpected attachments</li>
        <li>Verify requests through a separate channel (call the office)</li>
      </ol>
      <h3>Real Example</h3>
      <p><strong>Phishing email:</strong> "Dear Student, your Faith University email will expire tomorrow. Click here to verify: http://faith-university-login.fake-site.com"</p>
      <p><strong>Red flags:</strong> Urgency, suspicious URL (not the real university domain), asking for credentials via email.</p>
      <h3>What to Do If You Clicked</h3>
      <ol>
        <li>Change your password immediately</li>
        <li>Enable 2FA if not already on</li>
        <li>Contact IT support</li>
        <li>Monitor your accounts for suspicious activity</li>
      </ol>
    `
  },
  malware: {
    title: "Malware Protection",
    content: `
      <h3>What is Malware?</h3>
      <p>Malware (malicious software) is any program designed to harm your device, steal data, or disrupt operations. Common types include viruses, ransomware, trojans, and spyware.</p>
      <h3>Types of Malware</h3>
      <ul>
        <li><strong>Virus:</strong> Attaches to files and spreads when you open them</li>
        <li><strong>Ransomware:</strong> Encrypts your files and demands payment to unlock them</li>
        <li><strong>Trojan:</strong> Disguises itself as legitimate software</li>
        <li><strong>Spyware:</strong> Secretly monitors your activity and steals data</li>
        <li><strong>Worm:</strong> Self-replicates across networks without user action</li>
      </ul>
      <h3>How Malware Spreads</h3>
      <ul>
        <li>Email attachments from unknown senders</li>
        <li>Downloading cracked/pirated software</li>
        <li>Clicking malicious ads (malvertising)</li>
        <li>Infected USB drives</li>
        <li>Visiting compromised websites</li>
      </ul>
      <h3>Protection Strategies</h3>
      <ol>
        <li>Install reputable antivirus software (Windows Defender, Bitdefender, etc.)</li>
        <li>Keep your OS and apps updated (patches fix security holes)</li>
        <li>Don't download software from untrusted sources</li>
        <li>Back up important files regularly (cloud + external drive)</li>
        <li>Be cautious with email attachments and links</li>
      </ol>
      <h3>If You Suspect Malware</h3>
      <ol>
        <li>Disconnect from the internet immediately</li>
        <li>Run a full antivirus scan</li>
        <li>Don't pay ransomware demands (no guarantee of recovery)</li>
        <li>Contact IT support for assistance</li>
      </ol>
    `
  },
  "social-engineering": {
    title: "Social Engineering",
    content: `
      <h3>What is Social Engineering?</h3>
      <p>Social engineering is the art of manipulating people into giving up confidential information. Instead of hacking computers, attackers hack human psychology.</p>
      <h3>Common Social Engineering Attacks</h3>
      <ul>
        <li><strong>Pretexting:</strong> Creating a fabricated scenario to gain trust ("I'm from IT, I need your password to fix your account")</li>
        <li><strong>Baiting:</strong> Offering something enticing (free USB drive labeled "Salary Data 2024")</li>
        <li><strong>Tailgating:</strong> Following someone into a restricted area without credentials</li>
        <li><strong>Quid pro quo:</strong> Offering a service in exchange for information ("I'll give you free software if you tell me your login")</li>
      </ul>
      <h3>Psychological Triggers Attackers Use</h3>
      <ul>
        <li><strong>Urgency:</strong> "Do this NOW or lose access!"</li>
        <li><strong>Authority:</strong> Pretending to be a boss, professor, or IT admin</li>
        <li><strong>Fear:</strong> "Your account has been compromised!"</li>
        <li><strong>Greed:</strong> "You've won a prize!"</li>
        <li><strong>Helpfulness:</strong> Exploiting your desire to assist others</li>
      </ul>
      <h3>How to Defend Against Social Engineering</h3>
      <ol>
        <li>Verify identities through official channels (call the office, don't trust caller ID)</li>
        <li>Never share passwords, even with "IT support"</li>
        <li>Be skeptical of unsolicited requests for information</li>
        <li>Follow the principle of least privilege (only share what's necessary)</li>
        <li>Report suspicious interactions to security teams</li>
      </ol>
      <h3>Real-World Example</h3>
      <p>An attacker calls a student: "Hi, I'm from Faith University IT. We're upgrading the email system and need you to verify your password over the phone." The student, wanting to be helpful, provides their credentials. The attacker now has full access to their email.</p>
      <p><strong>Lesson:</strong> Legitimate IT departments NEVER ask for passwords via phone or email.</p>
    `
  },
  "data-protection": {
    title: "Data Protection",
    content: `
      <h3>Why Data Protection Matters</h3>
      <p>Your personal data (grades, financial info, health records, private messages) is valuable. Data breaches can lead to identity theft, financial loss, and reputational damage.</p>
      <h3>Types of Sensitive Data</h3>
      <ul>
        <li><strong>Personally Identifiable Information (PII):</strong> Name, ID number, address, phone</li>
        <li><strong>Financial data:</strong> Bank accounts, credit card numbers</li>
        <li><strong>Academic records:</strong> Grades, transcripts</li>
        <li><strong>Health information:</strong> Medical records, insurance details</li>
        <li><strong>Private communications:</strong> Emails, messages, photos</li>
      </ul>
      <h3>Encryption: Your Data's Bodyguard</h3>
      <p>Encryption scrambles data so only authorized parties can read it. Always use:</p>
      <ul>
        <li>HTTPS websites (look for the padlock icon)</li>
        <li>Encrypted messaging apps (Signal, WhatsApp)</li>
        <li>Disk encryption (BitLocker for Windows, FileVault for Mac)</li>
      </ul>
      <h3>Backup Strategy: The 3-2-1 Rule</h3>
      <ul>
        <li><strong>3</strong> copies of your data</li>
        <li><strong>2</strong> different storage types (e.g., laptop + external drive)</li>
        <li><strong>1</strong> copy offsite (cloud storage like Google Drive, OneDrive)</li>
      </ul>
      <h3>Best Practices</h3>
      <ol>
        <li>Lock your devices with PINs/passwords/biometrics</li>
        <li>Log out of accounts on shared computers</li>
        <li>Review app permissions (does that game really need your contacts?)</li>
        <li>Shred physical documents with sensitive info</li>
        <li>Be careful what you post on social media (oversharing = risk)</li>
      </ol>
    `
  },
  "safe-browsing": {
    title: "Safe Internet Usage",
    content: `
      <h3>The Internet: Opportunities and Risks</h3>
      <p>The internet is essential for learning, communication, and entertainment—but it's also full of threats. Safe browsing habits protect you from malware, scams, and privacy violations.</p>
      <h3>Recognizing Secure Websites</h3>
      <ul>
        <li>Look for <strong>https://</strong> (not http://) in the URL</li>
        <li>Check for the padlock icon in the address bar</li>
        <li>Verify the domain name (faithuniversity.edu.gh, not faith-university-login.com)</li>
        <li>Be wary of sites with excessive pop-ups or ads</li>
      </ul>
      <h3>Safe Downloading Practices</h3>
      <ol>
        <li>Only download from official sources (university portal, official app stores)</li>
        <li>Avoid cracked/pirated software (often bundled with malware)</li>
        <li>Scan downloads with antivirus before opening</li>
        <li>Read installation screens carefully (uncheck bundled toolbars/adware)</li>
      </ol>
      <h3>Wi-Fi Security</h3>
      <ul>
        <li><strong>Avoid public Wi-Fi</strong> for sensitive activities (banking, logging into university accounts)</li>
        <li>If you must use public Wi-Fi, use a VPN (Virtual Private Network)</li>
        <li>Turn off auto-connect to prevent joining rogue hotspots</li>
        <li>Forget networks after use on shared devices</li>
      </ul>
      <h3>Browser Security Settings</h3>
      <ol>
        <li>Keep your browser updated (Chrome, Firefox, Edge)</li>
        <li>Install reputable ad-blockers (uBlock Origin)</li>
        <li>Disable unnecessary extensions (each one is a potential vulnerability)</li>
        <li>Clear cookies/cache regularly</li>
        <li>Use private/incognito mode for sensitive browsing</li>
      </ol>
      <h3>Social Media Safety</h3>
      <ul>
        <li>Set profiles to private (control who sees your posts)</li>
        <li>Don't accept friend requests from strangers</li>
        <li>Think before posting (once it's online, it's forever)</li>
        <li>Be cautious with quizzes/surveys (often harvest personal data)</li>
      </ul>
    `
  }
};

// ===== 3. QUIZ QUESTIONS DATABASE =====
const quizQuestions = [
  { question: "Which of the following is the STRONGEST password?", options: ["password123", "FaithUniversity2024", "K9#mP$vL2@nQ", "johnsmith"], correct: 2, explanation: "The strongest password uses a mix of uppercase, lowercase, numbers, and symbols, and is not based on dictionary words or personal info." },
  { question: "You receive an email claiming to be from Faith University IT saying your account will expire in 24 hours unless you click a link and enter your password. What should you do?", options: ["Click the link immediately to avoid losing access", "Forward the email to all your classmates", "Contact IT support through the official university website or phone number", "Reply to the email asking for more details"], correct: 2, explanation: "This is a classic phishing attack. Never click links in suspicious emails. Verify through official channels instead." },
  { question: "What is the main purpose of Two-Factor Authentication (2FA)?", options: ["To make your password longer", "To add an extra layer of security beyond just a password", "To encrypt your files", "To speed up login time"], correct: 1, explanation: "2FA requires something you know (password) AND something you have (phone/authenticator), making it much harder for attackers to access your account." },
  { question: "Which of these is a sign of a phishing email?", options: ["Professional formatting and no spelling errors", "Urgent language like 'Act now or lose access!'", "Sent from an official university email address", "Contains a personalized greeting with your name"], correct: 1, explanation: "Phishing emails often create false urgency to pressure you into acting without thinking. Always verify suspicious requests." },
  { question: "What should you do if you suspect your computer has malware?", options: ["Continue using it normally", "Disconnect from the internet and run an antivirus scan", "Delete all your files immediately", "Ignore it—it will probably go away"], correct: 1, explanation: "Disconnecting prevents the malware from communicating with attackers or spreading. Then run a full antivirus scan." },
  { question: "What does HTTPS in a website URL indicate?", options: ["The site is faster", "The connection between your browser and the site is encrypted", "The site has been verified by Google", "The site is free to use"], correct: 1, explanation: "HTTPS encrypts data in transit, protecting it from being intercepted. Always look for HTTPS when entering sensitive information." },
  { question: "Someone calls you claiming to be from IT support and asks for your password to 'fix a problem' with your account. What should you do?", options: ["Give them your password—they're from IT, so it's safe", "Hang up and contact IT support through official channels", "Ask them to email you the request instead", "Give them a fake password"], correct: 1, explanation: "Legitimate IT departments NEVER ask for passwords via phone or email. This is a social engineering attack." },
  { question: "What is the 3-2-1 backup rule?", options: ["3 passwords, 2 devices, 1 cloud account", "3 copies of data, 2 different storage types, 1 offsite copy", "3 antivirus programs, 2 firewalls, 1 VPN", "3 users, 2 admins, 1 superuser"], correct: 1, explanation: "The 3-2-1 rule ensures you can recover data even if one storage fails: 3 copies, 2 media types, 1 offsite." },
  { question: "Which of these is the SAFEST way to connect to the internet for online banking?", options: ["Public Wi-Fi at a coffee shop", "Your home Wi-Fi with a strong password", "An open hotspot named 'Free_Internet'", "A friend's unsecured network"], correct: 1, explanation: "Your secured home Wi-Fi is safer than public networks, which can be monitored by attackers. For extra safety on public Wi-Fi, use a VPN." },
  { question: "What is ransomware?", options: ["Software that speeds up your computer", "Malware that encrypts your files and demands payment to unlock them", "A type of firewall", "An antivirus program"], correct: 1, explanation: "Ransomware locks your files and demands a ransom (usually in cryptocurrency) to restore access. Never pay—there's no guarantee of recovery." }
];

// ===== 4. SECTION NAVIGATION =====
function showSection(sectionName) {
  document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
  const targetSection = document.getElementById(sectionName + '-section');
  if (targetSection) {
    targetSection.classList.add('active');
  }
  if (sectionName === 'dashboard') updateDashboard();
  if (sectionName === 'quiz') loadQuiz();
  if (sectionName === 'progress') updateProgress();
}

// ===== 5. LOGIN (PHP-backed) =====
async function handleLogin(e) {
  e.preventDefault();
  const studentId = document.getElementById('student-id').value.trim();
  const password = document.getElementById('password').value;

  try {
    const response = await fetch('api/login.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ student_id: studentId, password: password })
    });
    const data = await response.json();

    if (response.ok && data.success) {
      currentUser = data.student;
      document.getElementById('student-name').textContent = currentUser.name;
      document.getElementById('login-alert').innerHTML = '';
      showSection('dashboard');
    } else {
      document.getElementById('login-alert').innerHTML =
        '<div class="alert alert-error">❌ ' + (data.error || 'Invalid Student ID or password') + '</div>';
    }
  } catch (err) {
    document.getElementById('login-alert').innerHTML =
      '<div class="alert alert-error">❌ Cannot reach the server. Open via http://localhost/cybersec-training/</div>';
  }
}

// ===== 6. REGISTRATION FORM TOGGLE =====
function showRegisterForm() {
  document.querySelector('#login-section form').style.display = 'none';
  const reg = document.getElementById('register-form');
  if (reg) reg.style.display = 'block';
}

function showLoginForm() {
  const reg = document.getElementById('register-form');
  if (reg) reg.style.display = 'none';
  const loginForm = document.querySelector('#login-section form');
  if (loginForm) loginForm.style.display = 'block';
  const alertBox = document.getElementById('register-alert');
  if (alertBox) alertBox.innerHTML = '';
}

// ===== 7. REGISTRATION (PHP-backed, no email required) =====
async function handleRegister(e) {
  e.preventDefault();
  const studentIdEl = document.getElementById('reg-student-id');
  const fullNameEl = document.getElementById('reg-full-name');
  const passwordEl = document.getElementById('reg-password');

  const studentId = studentIdEl ? studentIdEl.value.trim() : '';
  const fullName = fullNameEl ? fullNameEl.value.trim() : '';
  const password = passwordEl ? passwordEl.value : '';
  const email = ''; // email field removed — send empty

  function showMsg(html) {
    const box = document.getElementById('register-alert');
    if (box) box.innerHTML = html;
    else alert(html.replace(/<[^>]*>/g, ''));
  }

  try {
    const response = await fetch('api/register.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ student_id: studentId, full_name: fullName, email: email, password: password })
    });

    const text = await response.text();
    let data;
    try {
      data = JSON.parse(text);
    } catch (parseErr) {
      showMsg('<div class="alert alert-error">❌ Server returned: ' + text.substring(0, 300) + '</div>');
      return;
    }

    if (response.ok && data.success) {
      showMsg('<div class="alert alert-success">✅ Account created! You can now log in.</div>');
      setTimeout(showLoginForm, 1500);
    } else {
      showMsg('<div class="alert alert-error">❌ ' + (data.error || 'Registration failed') + '</div>');
    }
  } catch (err) {
    showMsg('<div class="alert alert-error">❌ Network error: ' + err.message + '</div>');
  }
}

// ===== 8. LOGOUT =====
function logout() {
  currentUser = null;
  completedModules = [];
  quizScores = [];
  currentQuizAnswers = {};
  const sid = document.getElementById('student-id');
  const pw = document.getElementById('password');
  if (sid) sid.value = '';
  if (pw) pw.value = '';
  const alertBox = document.getElementById('login-alert');
  if (alertBox) alertBox.innerHTML = '';
  showSection('login');
}

// ===== 9. DASHBOARD UPDATE =====
async function updateDashboard() {
  try {
    const response = await fetch('api/get_progress.php');
    const data = await response.json();
    if (response.ok && data.success) {
      const progress = data.progress.overall_percentage;
      document.getElementById('overall-progress').style.width = progress + '%';
      document.getElementById('progress-text').textContent = progress + '%';
      completedModules = data.progress.completed_modules.map(m => m.module_id);
      quizScores = data.progress.quiz_scores;
    }
  } catch (error) {
    console.warn('Failed to fetch dashboard data:', error);
  }
}

// ===== 10. OPEN MODULE =====
function openModule(moduleId) {
  const module = modules[moduleId];
  if (!module) return;
  document.getElementById('module-title').textContent = module.title;
  document.getElementById('module-content').innerHTML = module.content;
  document.getElementById('module-content-section').dataset.moduleId = moduleId;
  showSection('module-content');
}

// ===== 11. COMPLETE MODULE (saves to DB) =====
async function completeModule() {
  const moduleId = document.getElementById('module-content-section').dataset.moduleId;
  if (completedModules.includes(moduleId)) {
    alert('ℹ️ You already completed this module.');
    return;
  }
  completedModules.push(moduleId);
  try {
    await fetch('api/complete_module.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ module_id: moduleId })
    });
  } catch (err) {
    console.warn('Progress not saved to server:', err);
  }
  alert('✅ Module marked as complete!');
  showSection('modules');
}

// ===== 12. LOAD QUIZ =====
function loadQuiz() {
  const container = document.getElementById('quiz-container');
  container.innerHTML = '';
  currentQuizAnswers = {};
  document.getElementById('submit-quiz-btn').style.display = 'none';
  document.getElementById('quiz-result').innerHTML = '';

  quizQuestions.forEach((q, index) => {
    const questionDiv = document.createElement('div');
    questionDiv.className = 'quiz-question';

    let optionsHTML = '<div class="quiz-options">';
    q.options.forEach((option, optIndex) => {
      optionsHTML += `
        <div class="quiz-option" onclick="selectAnswer(${index}, ${optIndex})" id="q${index}-opt${optIndex}">
          ${String.fromCharCode(65 + optIndex)}. ${option}
        </div>
      `;
    });
    optionsHTML += '</div>';

    questionDiv.innerHTML = `
      <h4>Question ${index + 1}: ${q.question}</h4>
      ${optionsHTML}
      <div class="quiz-explanation" id="q${index}-explanation" style="display:none;"></div>
    `;
    container.appendChild(questionDiv);
  });

  document.getElementById('submit-quiz-btn').style.display = 'inline-block';
}

// ===== 13. SELECT QUIZ ANSWER =====
function selectAnswer(questionIndex, optionIndex) {
  quizQuestions[questionIndex].options.forEach((_, i) => {
    document.getElementById(`q${questionIndex}-opt${i}`).classList.remove('selected');
  });
  document.getElementById(`q${questionIndex}-opt${optionIndex}`).classList.add('selected');
  currentQuizAnswers[questionIndex] = optionIndex;
}

// ===== 14. SUBMIT QUIZ (saves score to DB) =====
async function submitQuiz() {
  const answeredCount = Object.keys(currentQuizAnswers).length;
  if (answeredCount < quizQuestions.length) {
    alert(`⚠️ Please answer all questions. You've answered ${answeredCount} out of ${quizQuestions.length}.`);
    return;
  }

  let score = 0;
  quizQuestions.forEach((q, index) => {
    const userAnswer = currentQuizAnswers[index];
    const optionElements = q.options.map((_, i) => document.getElementById(`q${index}-opt${i}`));
    optionElements.forEach(el => el.style.pointerEvents = 'none');

    if (userAnswer === q.correct) {
      score++;
      optionElements[userAnswer].classList.remove('selected');
      optionElements[userAnswer].classList.add('correct');
    } else {
      optionElements[userAnswer].classList.remove('selected');
      optionElements[userAnswer].classList.add('incorrect');
      optionElements[q.correct].classList.add('correct');
    }

    const explanationDiv = document.getElementById(`q${index}-explanation`);
    explanationDiv.textContent = '💡 ' + q.explanation;
    explanationDiv.style.display = 'block';
  });

  const percentage = Math.round((score / quizQuestions.length) * 100);

  // Save score to database
  try {
    await fetch(API_BASE + 'submit_quiz.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ score: score, total_questions: quizQuestions.length, percentage: percentage })
    });
  } catch (err) {
    console.warn('Score not saved:', err);
  }

  // Display result
  let resultMessage = '';
  let resultClass = '';
  if (percentage >= 80) {
    resultMessage = `🎉 Excellent! You scored ${score}/${quizQuestions.length} (${percentage}%). You have strong cybersecurity awareness!`;
    resultClass = 'alert-success';
  } else if (percentage >= 60) {
    resultMessage = `👍 Good job! You scored ${score}/${quizQuestions.length} (${percentage}%). Review the modules to improve further.`;
    resultClass = 'alert-info';
  } else {
    resultMessage = `📚 You scored ${score}/${quizQuestions.length} (${percentage}%). We recommend reviewing the learning modules and retaking the quiz.`;
    resultClass = 'alert-error';
  }

  document.getElementById('quiz-result').innerHTML = `<div class="alert ${resultClass}">${resultMessage}</div>`;
  document.getElementById('submit-quiz-btn').style.display = 'none';
}

// ===== 15. FETCH & DISPLAY PROGRESS =====
async function updateProgress() {
  try {
    const response = await fetch(API_BASE + 'get_progress.php');
    const data = await response.json();
    if (response.ok && data.success) {
      const p = data.progress;

      const modulesList = document.getElementById('completed-modules-list');
      if (p.completed_modules.length === 0) {
        modulesList.innerHTML = '<li style="color: var(--muted);">No modules completed yet.</li>';
      } else {
        modulesList.innerHTML = p.completed_modules.map(m => '<li>' + m.module_title + '</li>').join('');
      }

      const scoresDiv = document.getElementById('quiz-scores');
      if (p.quiz_scores.length === 0) {
        scoresDiv.innerHTML = '<p>No quizzes taken yet.</p>';
      } else {
        scoresDiv.innerHTML = p.quiz_scores.map((s, i) =>
          '<div class="score-item"><span>Quiz #' + (i + 1) + ' — ' + s.taken_at + '</span>' +
          '<span class="score-value">' + s.score + '/' + s.total_questions + ' (' + s.percentage + '%)</span></div>'
        ).join('');
      }

      document.getElementById('overall-progress').style.width = p.overall_percentage + '%';
      document.getElementById('progress-text').textContent = p.overall_percentage + '%';
      completedModules = p.completed_modules.map(m => m.module_id);
      quizScores = p.quiz_scores;
    }
  } catch (error) {
    console.warn('Could not load progress from server:', error);
  }
}

// ===== 16. INITIALIZATION =====
document.addEventListener('DOMContentLoaded', function() {
  showSection('login');
});