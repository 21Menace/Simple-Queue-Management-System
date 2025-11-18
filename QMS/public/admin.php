<?php
require_once __DIR__ . '/../src/session.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/csrf.php';
require_admin();
$title = 'Admin Panel - ' . APP_NAME;
include __DIR__ . '/../templates/header.php';
?>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body { padding: 2rem 1rem; }

.dashboard-container {
  max-width: 1400px;
  margin: 0 auto;
  position: relative;
  z-index: 2;
}

.dashboard-header {
  background: rgba(255, 255, 255, 0.25);
  backdrop-filter: blur(30px) saturate(180%);
  border-radius: 32px;
  padding: 2.5rem;
  margin-bottom: 2rem;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

.dashboard-header h1 {
  font-size: 2.5rem;
  font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 0.5rem;
  letter-spacing: -1px;
}

.dashboard-header p {
  color: #4b5563;
  font-size: 1rem;
  font-weight: 400;
}

.tabs-container {
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

.grid-2 {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 2rem;
  margin-bottom: 2rem;
}

.card {
  background: rgba(255, 255, 255, 0.25); /* 25% translucent */
  backdrop-filter: blur(16px) saturate(180%);
  -webkit-backdrop-filter: blur(16px) saturate(180%);
  border-radius: 32px;
  padding: 48px 40px 40px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
  transition: all 0.3s ease;
  border: 1px solid rgba(255,255,255,0.18); /* subtle border for glass effect */
}

.card:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
}

.card h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.form-group {
  margin-bottom: 1.25rem;
}

.form-group label {
  display: block;
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
}

.form-input, .form-select, .form-textarea {
  width: 100%;
  padding: 0.875rem 1rem;
  border: none;
  border-radius: 16px;
  font-size: 0.95rem;
  font-weight: 400;
  color: #1a1a1a;
  background: rgba(255, 255, 255, 0.25);
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

/* Select (dropdown) - match glass UI */
.form-select {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  padding-right: 3rem; /* room for chevron */
}

.select-wrapper {
  position: relative;
}

.select-wrapper::after {
  content: '';
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  width: 18px;
  height: 18px;
  pointer-events: none;
  opacity: 0.6;
  background-repeat: no-repeat;
  background-size: contain;
  background-image: url('data:image/svg+xml;utf8,<svg fill="none" stroke="gray" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M6 9l6 6 6-6"/></svg>');
}

.form-input:focus, .form-select:focus, .form-textarea:focus {
  outline: none;
  background: #ffffff;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
}

.form-textarea {
  min-height: 100px;
  resize: vertical;
  font-family: inherit;
}

.btn {
  padding: 0.875rem 1.75rem;
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

.btn-secondary {
  background: rgba(0, 0, 0, 0.06);
  color: #1a1a1a;
}

.btn-secondary:hover {
  background: rgba(0, 0, 0, 0.1);
}

.btn-full {
  width: 100%;
}

.services-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
}

.service-card {
  background: rgba(255, 255, 255, 0.25);
  backdrop-filter: blur(20px);
  border-radius: 20px;
  padding: 1.5rem;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
  transition: all 0.3s ease;
}

.service-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
}

.service-header {
  display: flex;
  justify-content: space-between;
  align-items: start;
  margin-bottom: 1rem;
}

.service-header h3 {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1a1a1a;
  flex: 1;
}

.service-badge {
  background: #1a1a1a;
  color: #ffffff;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
}

.service-description {
  color: #6b7280;
  font-size: 0.9rem;
  margin-bottom: 1rem;
  line-height: 1.5;
}

.service-actions {
  display: flex;
  justify-content: flex-end;
}

.btn-small {
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  border-radius: 20px;
}

.queues-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
  gap: 2rem;
}

.queue-card {
  background: rgba(255, 255, 255, 0.25);
  backdrop-filter: blur(20px);
  border-radius: 24px;
  padding: 2rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
}

.queue-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
}

.queue-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.queue-header h3 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1a1a1a;
}

.queue-id {
  background: #1a1a1a;
  color: #ffffff;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 600;
}

.stats-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.stat-box {
  background: rgba(0, 0, 0, 0.03);
  border-radius: 16px;
  padding: 1.5rem;
  text-align: center;
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
  font-size: 2.5rem;
  font-weight: 700;
  color: #1a1a1a;
}

.serving-box {
  background: rgba(251, 191, 36, 0.15);
  border: 1px solid rgba(251, 191, 36, 0.3);
  border-radius: 16px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
}

.serving-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.serving-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #92400e;
}

.serving-time {
  font-size: 0.75rem;
  color: #92400e;
}

.serving-ticket {
  font-size: 3rem;
  font-weight: 700;
  color: #d97706;
}

.no-serving {
  background: rgba(0, 0, 0, 0.03);
  border-radius: 16px;
  padding: 1.5rem;
  text-align: center;
  color: #6b7280;
  font-weight: 400;
  margin-bottom: 1.5rem;
}

.queue-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
}

.btn-danger {
  background: rgba(239, 68, 68, 0.1);
  color: #dc2626;
}

.btn-danger:hover {
  background: rgba(239, 68, 68, 0.15);
}

.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  color: #6b7280;
}

.empty-state-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
  opacity: 0.3;
}

.empty-state-text {
  font-size: 1rem;
  font-weight: 400;
}

.notification {
  position: fixed;
  top: 2rem;
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

.notification-title {
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.notification.success .notification-title {
  color: #059669;
}

.notification.error .notification-title {
  color: #dc2626;
}

.notification-message {
  color: #6b7280;
  font-size: 0.9rem;
}

.loading {
  opacity: 0.6;
  pointer-events: none;
}

@media (max-width: 768px) {
  .dashboard-header h1 {
    font-size: 2rem;
  }
  
  .grid-2, .services-grid, .queues-grid {
    grid-template-columns: 1fr;
  }
  
  .tabs-nav {
    display: flex;
    flex-direction: column;
    width: 100%;
  }

  .tab-button {
    width: 100%;
  }
  
  .queue-actions {
    flex-direction: column;
  }
  
  .btn {
    width: 100%;
  }
}

</style>

<div class="dashboard-container">
  <div class="dashboard-header">
    <h1>Admin Dashboard</h1>
    <p>Manage campus service queues with ease - open, close, and monitor in real-time</p>
  </div>

  <div class="tabs-container">
    <div class="tabs-nav">
      <button class="tab-button active" data-tab="services">
        Services Management
      </button>
      <button class="tab-button" data-tab="queues">
        Active Queues
      </button>
    </div>

    <div id="servicesContent" class="tab-content active">
      <div class="grid-2">
        <div class="card">
          <h2>Create New Service</h2>
          <form id="serviceForm">
            <div class="form-group">
              <label>Service Name</label>
              <input type="text" name="name" class="form-input" placeholder="e.g., Student ID Services" required>
            </div>
            <div class="form-group">
              <label>Description (Optional)</label>
              <textarea name="description" class="form-textarea" placeholder="A brief description for students..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-full">
              Create Service
            </button>
          </form>
        </div>

        <div class="card">
          <h2>Open New Queue</h2>
          <form id="queueForm">
            <div class="form-group">
              <label>Select Service</label>
              <div class="select-wrapper">
                <select name="service_id" id="serviceSelect" class="form-select" required>
                  <option value="">Choose a service...</option>
                </select>
              </div>
            </div>
            <button type="submit" class="btn btn-secondary btn-full">
              Open Queue
            </button>
          </form>
        </div>
      </div>

      <div class="card">
        <h2>Available Services</h2>
        <div id="servicesList">
          <div class="empty-state">
            <div class="empty-state-icon">⏳</div>
            <div class="empty-state-text">Loading services...</div>
          </div>
        </div>
      </div>
    </div>

    <div id="queuesContent" class="tab-content">
      <div class="card">
        <h2>Active Queues</h2>
        <div id="queues">
          <div class="empty-state">
            <div class="empty-state-icon">📭</div>
            <div class="empty-state-text">No active queues. Open a queue to get started.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- background shapes removed -->

<script>
const csrfHeader = () => {
  const m = document.querySelector('meta[name="csrf-token"]');
  return m ? { 'X-CSRF-Token': m.content } : {};
};

const api = (p, opts = {}) => 
  fetch('api/' + p, { 
    credentials: 'include', 
    ...opts,
    headers: {
      'Content-Type': 'application/json',
      ...(opts.headers || {}),
      ...csrfHeader()
    }
  }).then(async r => {
    const data = await r.json();
    if (!r.ok) {
      const error = new Error(data.error || 'Something went wrong');
      error.response = data;
      throw error;
    }
    return data;
  });

function setupTabs() {
  const buttons = document.querySelectorAll('.tab-button');
  const contents = document.querySelectorAll('.tab-content');

  buttons.forEach(button => {
    button.addEventListener('click', () => {
      const tab = button.dataset.tab;
      
      buttons.forEach(btn => btn.classList.remove('active'));
      contents.forEach(content => content.classList.remove('active'));
      
      button.classList.add('active');
      document.getElementById(tab + 'Content').classList.add('active');
      
      if (tab === 'queues') {
        refreshQueues();
      }
    });
  });
}

async function refreshServices() {
  try {
  const data = await api('services/list.php');
    const services = data.services || [];
    
    const sel = document.getElementById('serviceSelect');
    sel.innerHTML = '<option value="">Choose a service...</option>' + 
      services.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
    
    const servicesList = document.getElementById('servicesList');
    if (services.length === 0) {
      servicesList.innerHTML = `
        <div class="empty-state">
          <div class="empty-state-icon">📝</div>
          <div class="empty-state-text">No services yet. Create your first service above!</div>
        </div>
      `;
      return;
    }
    
    servicesList.innerHTML = `
      <div class="services-grid">
        ${services.map(service => `
          <div class="service-card">
            <div class="service-header">
              <h3>${service.name}</h3>
              <span class="service-badge">#${service.id}</span>
            </div>
            <p class="service-description">${service.description || 'No description provided.'}</p>
            <div class="service-actions">
              <button onclick="openQueueForService(${service.id}, '${service.name.replace(/'/g, '\\\'')}')" 
                class="btn btn-secondary btn-small">
                Open Queue
              </button>
            </div>
          </div>
        `).join('')}
      </div>
    `;
  } catch (error) {
    console.error('Failed to load services:', error);
    showError('Failed to load services. Please try again.');
  }
}

async function refreshQueues() {
  try {
  const data = await api('queues/queues_list.php');
    const queues = data.queues || [];
    const queuesEl = document.getElementById('queues');
    
    if (queues.length === 0) {
      queuesEl.innerHTML = `
        <div class="empty-state">
          <div class="empty-state-icon">📭</div>
          <div class="empty-state-text">No active queues. Open a queue to get started.</div>
        </div>
      `;
      return;
    }
    
    queuesEl.innerHTML = `
      <div class="queues-grid">
        ${queues.map(queue => `
          <div class="queue-card">
            <div class="queue-header">
              <h3>${queue.service_name}</h3>
              <span class="queue-id">Queue #${queue.id}</span>
            </div>
            
            <div class="stats-grid">
              <div class="stat-box">
                <div class="stat-label">Waiting</div>
                <div class="stat-value">${queue.waiting_count || 0}</div>
              </div>
              <div class="stat-box">
                <div class="stat-label">Serving</div>
                <div class="stat-value">${queue.serving_count || 0}</div>
              </div>
            </div>

            ${queue.current_ticket ? `
              <div class="serving-box">
                <div class="serving-header">
                  <div class="serving-label">🎯 Now Serving</div>
                  <div class="serving-time">${new Date(queue.current_ticket.called_at).toLocaleTimeString()}</div>
                </div>
                <div class="serving-ticket">${queue.current_ticket.ticket_number}</div>
              </div>
            ` : '<div class="no-serving">No one is currently being served</div>'}
            
            <div class="queue-actions">
              <button onclick="serveNext(${queue.id})" class="btn btn-primary">
                Call Next Ticket
              </button>
              <button onclick="closeQueue(${queue.id})" class="btn btn-danger">
                Close Queue
              </button>
            </div>
          </div>
        `).join('')}
      </div>
    `;
  } catch (error) {
    console.error('Failed to load queues:', error);
    showError('Failed to load queues. Please try again.');
  }
}

async function serveNext(queueId) {
  try {
  const res = await api('queues/queues_next.php', {
      method: 'POST',
      body: JSON.stringify({ queue_id: queueId })
    });
    
    if (res.closed) {
      showSuccess('Queue closed. No more waiting tickets.');
    } else if (res.served) {
      const message = res.next_notified 
        ? `Now serving ${res.served.student_name} (${res.served.student_id}). Next student has been notified!`
        : `Now serving ${res.served.student_name} (${res.served.student_id})`;
      showSuccess(message);
    }
    
    await refreshQueues();
  } catch (error) {
    console.error('Error serving next ticket:', error);
    showError(error.message || 'Failed to serve next ticket');
  }
}

async function closeQueue(queueId) {
  if (!confirm('Are you sure you want to close this queue? This will cancel all waiting tickets.')) {
    return;
  }
  
  try {
  await api('queues/queues_close.php', {
      method: 'POST',
      body: JSON.stringify({ queue_id: queueId })
    });
    
    showSuccess('Queue closed successfully');
    await refreshQueues();
  } catch (error) {
    console.error('Error closing queue:', error);
    showError(error.message || 'Failed to close queue');
  }
}

function openQueueForService(serviceId, serviceName) {
  const select = document.getElementById('serviceSelect');
  select.value = serviceId;
  
  document.querySelector('[data-tab="queues"]').click();
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function showError(message) {
  showNotification(message, 'error');
}

function showSuccess(message) {
  showNotification(message, 'success');
}

function showNotification(message, type = 'info') {
  const notification = document.createElement('div');
  notification.className = `notification ${type}`;
  notification.innerHTML = `
    <div class="notification-title">${type === 'error' ? '❌ Error' : '✅ Success'}</div>
    <div class="notification-message">${message}</div>
  `;
  
  document.body.appendChild(notification);
  setTimeout(() => {
    notification.style.animation = 'slideIn 0.3s ease reverse';
    setTimeout(() => notification.remove(), 300);
  }, 5000);
}

document.getElementById('serviceForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const form = e.currentTarget;
  const button = form.querySelector('button[type="submit"]');
  const originalText = button.innerHTML;
  
  try {
    button.disabled = true;
    button.innerHTML = 'Creating...';
    form.classList.add('loading');
    
    const fd = new FormData(form);
  await api('services/services_create.php', {
      method: 'POST',
      body: JSON.stringify(Object.fromEntries(fd.entries()))
    });
    
    showSuccess('Service created successfully!');
    form.reset();
    await refreshServices();
  } catch (error) {
    console.error('Error creating service:', error);
    showError(error.message || 'Failed to create service');
  } finally {
    button.disabled = false;
    button.innerHTML = originalText;
    form.classList.remove('loading');
  }
});

document.getElementById('queueForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const form = e.currentTarget;
  const button = form.querySelector('button[type="submit"]');
  const originalText = button.innerHTML;
  
  try {
    button.disabled = true;
    button.innerHTML = 'Opening...';
    form.classList.add('loading');
    
    const fd = new FormData(form);
  await api('queues/queues_create.php', {
      method: 'POST',
      body: JSON.stringify(Object.fromEntries(fd.entries()))
    });
    
    showSuccess('Queue opened successfully!');
    form.reset();
    await refreshQueues();
    
    document.querySelector('[data-tab="queues"]').click();
  } catch (error) {
    console.error('Error opening queue:', error);
    showError(error.message || 'Failed to open queue');
  } finally {
    button.disabled = false;
    button.innerHTML = originalText;
    form.classList.remove('loading');
  }
});

setupTabs();
refreshServices();
refreshQueues();
setInterval(refreshQueues, 30000);
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>