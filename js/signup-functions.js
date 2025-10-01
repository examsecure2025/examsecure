document.querySelectorAll('.google-btn').forEach(btn => {
    btn.addEventListener('mouseover', function(e) {
      const rect = btn.getBoundingClientRect();
      const ripple = document.createElement('span');
      ripple.className = 'ripple';
      ripple.style.width = ripple.style.height = Math.max(rect.width, rect.height) + 'px';
      ripple.style.left = (e.clientX - rect.left - rect.width/2) + 'px';
      ripple.style.top = (e.clientY - rect.top - rect.height/2) + 'px';
      btn.appendChild(ripple);
      ripple.addEventListener('animationend', () => ripple.remove());
    });
  });