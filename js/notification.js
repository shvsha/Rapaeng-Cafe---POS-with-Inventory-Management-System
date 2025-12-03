// Notification System
function showNotification(message, type = 'info', duration = 3000) {
  // Remove existing notification if any
  const existingNotif = document.querySelector('.notification-container');
  if (existingNotif) {
    existingNotif.remove();
  }

  // Create notification container
  const container = document.createElement('div');
  container.className = 'notification-container';
  
  // Create notification box
  const notif = document.createElement('div');
  notif.className = `notification notification-${type}`;
  notif.innerHTML = `
    <div class="notification-content">
      <span class="notification-icon">${getNotificationIcon(type)}</span>
      <span class="notification-message">${escapeHtml(message)}</span>
      <button class="notification-close" onclick="this.parentElement.parentElement.remove()">×</button>
    </div>
  `;
  
  container.appendChild(notif);
  document.body.appendChild(container);
  
  // Auto remove
  setTimeout(() => {
    if (container.parentElement) {
      container.remove();
    }
  }, duration);
}

function getNotificationIcon(type) {
  switch(type) {
    case 'success': return '✓';
    case 'error': return '✕';
    case 'warning': return '⚠';
    case 'info':
    default: return 'ℹ';
  }
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

// Add notification styles to page
if (!document.getElementById('notification-styles')) {
  const style = document.createElement('style');
  style.id = 'notification-styles';
  style.textContent = `
    .notification-container {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
      max-width: 400px;
    }
    
    .notification {
      background: white;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      overflow: hidden;
      animation: slideInNotif 0.3s ease-out;
    }
    
    .notification-content {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 16px 20px;
    }
    
    .notification-icon {
      font-size: 20px;
      font-weight: bold;
      flex-shrink: 0;
    }
    
    .notification-message {
      flex: 1;
      font-size: 14px;
      color: #333;
    }
    
    .notification-close {
      background: none;
      border: none;
      font-size: 20px;
      cursor: pointer;
      color: #999;
      padding: 0;
      flex-shrink: 0;
      transition: color 0.2s;
    }
    
    .notification-close:hover {
      color: #333;
    }
    
    .notification-success {
      border-left: 4px solid #4CAF50;
    }
    
    .notification-success .notification-icon {
      color: #4CAF50;
    }
    
    .notification-error {
      border-left: 4px solid #f44336;
    }
    
    .notification-error .notification-icon {
      color: #f44336;
    }
    
    .notification-warning {
      border-left: 4px solid #ff9800;
    }
    
    .notification-warning .notification-icon {
      color: #ff9800;
    }
    
    .notification-info {
      border-left: 4px solid #2196F3;
    }
    
    .notification-info .notification-icon {
      color: #2196F3;
    }
    
    @keyframes slideInNotif {
      from {
        transform: translateX(400px);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }
  `;
  document.head.appendChild(style);
}
