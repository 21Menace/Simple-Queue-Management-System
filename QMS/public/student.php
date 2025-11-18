<?php
require_once __DIR__ . '/../src/session.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/csrf.php';
require_login();

$pdo = get_pdo();
$queues = $pdo->query('SELECT id, name, description FROM queues WHERE active = 1 ORDER BY name')->fetchAll();
$title = 'Student Portal - ' . APP_NAME;
include __DIR__ . '/../templates/header.php';
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
  min-height: 100vh;
  background: url('https://images.pexels.com/photos/34211745/pexels-photo-34211745.jpeg') center/cover no-repeat;
  position: relative;
  padding: 2rem 1rem;
}

body::before {
  content: '';
  position: absolute;
  inset: 0;
  background: rgba(255, 255, 255, 0.3);
  backdrop-filter: blur(2px);
}

.portal-container {
  max-width: 1400px;
  margin: 0 auto;
  position: relative;
  z-index: 2;
}

.portal-header {
  background: rgba(255, 255, 255, 0.25);
  backdrop-filter: blur(30px) saturate(180%);
  border-radius: 32px;
  padding: 2.5rem;
  margin-bottom: 2rem;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

.portal-header h1 {
  font-size: 2.5rem;
  font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 0.5rem;
  letter-spacing: -1px;
}

.portal-header p {
  color: #4b5563;
  font-size: 1rem;
  font-weight: 400;
}

.content-card {
  background: rgba(255, 255, 255, 0.25);
  backdrop-filter: blur(30px) saturate(180%);
  border-radius: 32px;
  padding: 2rem;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

.tabs-nav {
  display: inline-flex;
  background: rgba(0, 0, 0, 0.04);
  border-radius: 24px;
  padding: 6px;
  margin-bottom: 2rem;
}

.tab-button {
  padding: 0.75rem 1.5rem;
  font-size: 0.95rem;
  font-weight: 600;
  background: transparent;
  border: none;
  border-radius: 20px;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.3s ease;
}

.tab-button.active {
  background: #1a1a1a;
  color: #ffffff;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.tab-button:hover:not(.active) {
  color: #1a1a1a;
  background: rgba(0, 0, 0, 0.06);
}

.tab-content {
  display: none;
}

.tab-content.active {
  display: block;
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.section-header {
  margin-bottom: 2rem;
}

.section-header h2 {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 0.5rem;
}

.section-header p {
  color: #6b7280;
  font-size: 0.95rem;
}

.queues-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 1.5rem;
}

.queue-card {
   background: rgba(255, 255, 255, 0.25); /* 25% translucent */
  backdrop-filter: blur(16px) saturate(180%);
  -webkit-backdrop-filter: blur(16px) saturate(180%);
  border-radius: 32px;
  padding: 48px 40px 40px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
  transition: all 0.3s ease;
  border: 1px solid rgba(255,255,255,0.18);
}

.queue-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
}

.queue-header {
  display: flex;
  justify-content: space-between;
  align-items: start;
  margin-bottom: 1rem;
}

.queue-header h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1a1a1a;
  flex: 1;
}

.queue-badge {
  background: #1a1a1a;
  color: #ffffff;
  padding: 0.375rem 0.875rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
}

.queue-description {
  color: #6b7280;
  font-size: 0.9rem;
  margin-bottom: 1.25rem;
  line-height: 1.5;
}

.btn {
  padding: 0.875rem 1.5rem;
  border: none;
  border-radius: 24px;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-primary {
  background: #1a1a1a;
  color: #ffffff;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
}

.btn-primary:hover {
  background: #2d2d2d;
  transform: translateY(-2px);
  box-shadow: 0 6px 24px rgba(0, 0, 0, 0.2);
}

.btn-primary:active {
  transform: translateY(0);
}

.btn-full {
  width: 100%;
}

.btn-secondary {
  background: rgba(0, 0, 0, 0.06);
  color: #1a1a1a;
}

.btn-secondary:hover {
  background: rgba(0, 0, 0, 0.1);
}

.status-card {
  background: rgba(255, 255, 255, 0.25);
  backdrop-filter: blur(20px);
  border-radius: 24px;
  padding: 2.5rem;
  text-align: center;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
}

.status-position {
  font-size: 3rem;
  font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 1rem;
}

.status-queue-name {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 0.5rem;
}

.status-label {
  color: #6b7280;
  font-size: 0.95rem;
  margin-bottom: 2rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
  margin: 2rem 0;
}

.stat-box {
  background: rgba(0, 0, 0, 0.03);
  border-radius: 16px;
  padding: 1.5rem;
  transition: all 0.3s ease;
}

.stat-box:hover {
  transform: scale(1.03);
  background: rgba(0, 0, 0, 0.05);
}

.stat-label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #6b7280;
  margin-bottom: 0.5rem;
}

.stat-value {
  font-size: 2rem;
  font-weight: 700;
  color: #1a1a1a;
}

.empty-state {
  text-align: center;
  padding: 3rem 2rem;
}

.empty-state-icon {
  font-size: 3.5rem;
  margin-bottom: 1rem;
  opacity: 0.3;
}

.empty-state-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #6b7280;
  margin-bottom: 0.5rem;
}

.empty-state-text {
  color: #9ca3af;
  font-size: 0.95rem;
  margin-bottom: 1.5rem;
}

.update-time {
  text-align: center;
  color: #9ca3af;
  font-size: 0.85rem;
  margin-top: 2rem;
}

.notification {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  background: rgba(255, 255, 255, 0.25);
  backdrop-filter: blur(20px);
  border-radius: 20px;
  padding: 1.5rem;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  z-index: 1000;
  min-width: 320px;
  animation: slideIn 0.3s ease;
}

@keyframes slideIn {
  from { transform: translateX(400px); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

.notification.success {
  border-left: 4px solid #10b981;
}

.notification.error {
  border-left: 4px solid #ef4444;
}

.notification.urgent {
  border-left: 4px solid #f59e0b;
  animation: slideIn 0.3s ease, pulse 2s infinite;
  background: rgba(254, 243, 199, 0.95);
}

@keyframes pulse {
  0%, 100% { transform: scale(1) translateX(0); }
  50% { transform: scale(1.02) translateX(0); }
}

.notification-title {
  font-weight: 600;
  margin-bottom: 0.5rem;
  font-size: 1.05rem;
}

.notification.success .notification-title {
  color: #059669;
}

.notification.error .notification-title {
  color: #dc2626;
}

.notification.urgent .notification-title {
  color: #d97706;
}

.notification-message {
  color: #6b7280;
  font-size: 0.9rem;
  line-height: 1.4;
}

.notification-close {
  position: absolute;
  top: 0.75rem;
  right: 0.75rem;
  background: rgba(0, 0, 0, 0.1);
  border: none;
  color: #6b7280;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  cursor: pointer;
  font-size: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.notification-close:hover {
  background: rgba(0, 0, 0, 0.15);
  color: #374151;
}

.loading-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999;
}

.loading-content {
  background: #ffffff;
  border-radius: 20px;
  padding: 2rem;
  max-width: 400px;
  width: 90%;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.loading-inner {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.spinner {
  width: 24px;
  height: 24px;
  border: 3px solid #e5e7eb;
  border-top-color: #1a1a1a;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

@media (max-width: 768px) {
  .portal-header h1 {
    font-size: 2rem;
  }
  
  .queues-grid, .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .notification {
    bottom: 1rem;
    right: 1rem;
    left: 1rem;
    min-width: auto;
  }
}
</style>

<div class="portal-container">
  <div class="portal-header">
    <h1>Student Portal</h1>
    <p>Join queues and track your position in real-time</p>
  </div>

  <div class="content-card">
    <div class="tabs-nav">
      <button id="joinTab" class="tab-button active">Join Queue</button>
      <button id="statusTab" class="tab-button">My Position</button>
    </div>

    <div id="joinContent" class="tab-content active">
      <div class="section-header">
        <h2>Available Services</h2>
        <p>Select a service to join the queue</p>
      </div>

      <div class="queues-grid">
        <?php foreach ($queues as $q): ?>
          <div class="queue-card">
            <div class="queue-header">
              <h3><?= htmlspecialchars($q['name']) ?></h3>
              <span class="queue-badge">
                <span id="pill-q-<?= (int)$q['id'] ?>">0</span> waiting
              </span>
            </div>
            <p class="queue-description">
              <?= !empty($q['description']) ? htmlspecialchars($q['description']) : 'Join this queue to get service' ?>
            </p>
            <button 
              onclick="joinQueue(<?= (int)$q['id'] ?>, '<?= addslashes(htmlspecialchars($q['name'])) ?>')"
              class="btn btn-primary btn-full">
              Join Queue
            </button>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div id="statusContent" class="tab-content">
      <div class="section-header">
        <h2>Your Queue Status</h2>
        <p>Track your position in real-time</p>
      </div>

      <div id="statusContainer">
        <div class="empty-state">
          <div class="empty-state-icon">📭</div>
          <div class="empty-state-title">Not in any queue</div>
          <p class="empty-state-text">Join a queue to see your status here</p>
          <button onclick="document.getElementById('joinTab').click()" class="btn btn-primary">
            Join a Queue
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// CSRF Token
function csrfHeader() {
  const m = document.querySelector('meta[name="csrf-token"]');
  return m ? { 'X-CSRF-Token': m.content } : {};
}

// Fetch Utility
async function fetchJSON(url, opts = {}) {
  const res = await fetch(url, {
    ...opts,
    headers: {
      'Content-Type': 'application/json',
      ...(opts.headers || {}),
      ...csrfHeader()
    }
  });
  
  if (!res.ok) {
    const error = await res.json().catch(() => ({}));
    throw new Error(error.message || 'Request failed');
  }
  
  return res.json();
}

// Tab Management
function setupTabs() {
  const joinTab = document.getElementById('joinTab');
  const statusTab = document.getElementById('statusTab');
  const joinContent = document.getElementById('joinContent');
  const statusContent = document.getElementById('statusContent');

  joinTab.addEventListener('click', () => {
    joinTab.classList.add('active');
    joinContent.classList.add('active');
    statusTab.classList.remove('active');
    statusContent.classList.remove('active');
  });

  statusTab.addEventListener('click', () => {
    statusTab.classList.add('active');
    statusContent.classList.add('active');
    joinTab.classList.remove('active');
    joinContent.classList.remove('active');
    refreshStatus();
  });
}

// Status Management
async function refreshStatus() {
  const statusContainer = document.getElementById('statusContainer');
  
  try {
    const data = await fetchJSON('api/queue_status.php');
    
    if (data.in_queue) {
      statusContainer.innerHTML = `
        <div class="status-card">
          <div class="status-position">#${data.position}</div>
          <div class="status-queue-name">${data.queue_name}</div>
          <div class="status-label">Your current position</div>
          
          <div class="stats-grid">
            <div class="stat-box">
              <div class="stat-label">Ahead of you</div>
              <div class="stat-value">${parseInt(data.position) - 1 || 'None'}</div>
            </div>
            <div class="stat-box">
              <div class="stat-label">Estimated wait</div>
              <div class="stat-value">${data.eta || '~'}</div>
            </div>
          </div>
          
          <button onclick="leaveQueue()" class="btn btn-secondary btn-full">
            Leave Queue
          </button>
          
          <div class="update-time">
            Last updated: ${new Date().toLocaleTimeString()}
          </div>
        </div>
      `;
    } else {
      statusContainer.innerHTML = `
        <div class="empty-state">
          <div class="empty-state-icon">📭</div>
          <div class="empty-state-title">Not in any queue</div>
          <p class="empty-state-text">Join a queue to see your status here</p>
          <button onclick="document.getElementById('joinTab').click()" class="btn btn-primary">
            Join a Queue
          </button>
        </div>
      `;
    }
  } catch (error) {
    console.error('Error refreshing status:', error);
    showNotification('Failed to load queue status', 'error');
  }
}

async function refreshCounts() {
  const pills = document.querySelectorAll('[id^="pill-q-"]');
  
  for (const pill of pills) {
    const id = pill.id.split('-').pop();
    try {
      const data = await fetchJSON(`api/queue_status.php?queue_id=${id}`);
      pill.textContent = data.waiting || '0';
    } catch (error) {
      console.error(`Error refreshing count for queue ${id}:`, error);
      pill.textContent = '-';
    }
  }
}

// Queue Actions
async function joinQueue(queueId, queueName) {
  try {
    showLoading('Joining queue...');
    
    const data = await fetchJSON('api/join_queue.php', {
      method: 'POST',
      body: JSON.stringify({ queue_id: queueId })
    });
    
    if (data.success) {
      showNotification(`Joined ${queueName} queue`, 'success');
      await Promise.all([refreshStatus(), refreshCounts()]);
      document.getElementById('statusTab').click();
    } else {
      throw new Error(data.error || 'Failed to join queue');
    }
  } catch (error) {
    console.error('Error joining queue:', error);
    showNotification(error.message || 'Failed to join queue', 'error');
  } finally {
    hideLoading();
  }
}

async function leaveQueue() {
  if (!confirm('Are you sure you want to leave the queue?')) {
    return;
  }
  
  try {
    showLoading('Leaving queue...');
    
    const data = await fetchJSON('api/leave_queue.php', {
      method: 'POST'
    });
    
    if (data.success) {
      showNotification('Left the queue', 'success');
      await Promise.all([refreshStatus(), refreshCounts()]);
    } else {
      throw new Error(data.error || 'Failed to leave queue');
    }
  } catch (error) {
    console.error('Error leaving queue:', error);
    showNotification(error.message || 'Failed to leave queue', 'error');
  } finally {
    hideLoading();
  }
}

// UI Helpers
function showLoading(message = 'Loading...') {
  let loader = document.getElementById('loading-overlay');
  
  if (!loader) {
    loader = document.createElement('div');
    loader.id = 'loading-overlay';
    loader.className = 'loading-overlay';
    loader.innerHTML = `
      <div class="loading-content">
        <div class="loading-inner">
          <div class="spinner"></div>
          <div>${message}</div>
        </div>
      </div>
    `;
    document.body.appendChild(loader);
  }
}

function hideLoading() {
  const loader = document.getElementById('loading-overlay');
  if (loader) loader.remove();
}

function showNotification(message, type = 'info') {
  const notification = document.createElement('div');
  notification.className = `notification ${type}`;
  notification.innerHTML = `
    <div class="notification-title">${type === 'error' ? '❌ Error' : type === 'urgent' ? '🎯 Alert' : '✅ Success'}</div>
    <div class="notification-message">${message}</div>
    <button onclick="this.parentElement.remove()" class="notification-close">×</button>
  `;
  
  document.body.appendChild(notification);
  
  setTimeout(() => {
    if (notification.parentElement) {
      notification.style.animation = 'slideIn 0.3s ease reverse';
      setTimeout(() => notification.remove(), 300);
    }
  }, type === 'urgent' ? 15000 : 5000);
}

// Push Notification System
let lastKnownPosition = null;

async function requestNotificationPermission() {
  if (!('Notification' in window)) {
    console.log('This browser does not support notifications');
    return false;
  }

  if (Notification.permission === 'granted') {
    return true;
  }

  if (Notification.permission !== 'denied') {
    const permission = await Notification.requestPermission();
    return permission === 'granted';
  }

  return false;
}

function showPushNotification(title, body, options = {}) {
  if (Notification.permission === 'granted') {
    const notification = new Notification(title, {
      body: body,
      icon: '/favicon.ico',
      badge: '/favicon.ico',
      tag: 'queue-notification',
      requireInteraction: true,
      ...options
    });

    notification.onclick = function() {
      window.focus();
      notification.close();
    };
  }
}

async function checkQueuePosition() {
  try {
    const data = await fetchJSON('api/queue_status.php');
    
    if (data.in_queue) {
      const currentPosition = data.position;
      
      // Check if user is next in line
      if (currentPosition === 1 && lastKnownPosition !== 1) {
        showNotification(
          '🎯 You are next in line!',
          `Please proceed to ${data.queue_name} service desk now.`,
          'urgent'
        );
        
        showPushNotification(
          '🎯 You are Next!',
          `You are next in line for ${data.queue_name}. Please proceed to the service desk.`,
          { vibrate: [200, 100, 200] }
        );
      }
      // Check if user moved up significantly
      else if (currentPosition <= 3 && lastKnownPosition > 3) {
        showNotification(
          '📍 Position Update',
          `You are now #${currentPosition} in line for ${data.queue_name}`,
          'urgent'
        );
      }
      
      lastKnownPosition = currentPosition;
      refreshStatus();
    } else {
      lastKnownPosition = null;
    }
  } catch (error) {
    console.error('Error checking queue position:', error);
  }
}

// Initialize
setupTabs();
refreshStatus();
refreshCounts();

// Request notification permission after page load
window.addEventListener('load', () => {
  requestNotificationPermission();
});

// Auto-refresh and position checking
setInterval(() => {
  refreshStatus();
  refreshCounts();
  checkQueuePosition();
}, 10000); // Check every 10 seconds

// Immediate position check
checkQueuePosition();
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>