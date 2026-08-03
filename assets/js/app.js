(function () {
  function setTheme(mode) {
    document.documentElement.setAttribute('data-theme', mode);
    document.cookie = 'tc_theme=' + mode + ';path=/;max-age=' + (60 * 60 * 24 * 365);
    const btn = document.getElementById('themeToggle');
    if (btn) {
      const icon = btn.querySelector('i');
      if (icon) icon.className = mode === 'dark' ? 'bi bi-sun-fill me-1' : 'bi bi-moon-stars-fill me-1';
      btn.querySelector('.mode-text') && (btn.querySelector('.mode-text').textContent = mode === 'dark' ? 'Bright Mode' : 'Dark Mode');
    }
  }

  window.TableCraft = {
    setTheme,
    parseRows: function (raw, columns) {
      const lines = (raw || '').trim().split(/\r?\n/).filter(Boolean);
      return lines.map(line => {
        const parts = line.split(/\s*\|\s*|\t|,/).map(v => v.trim());
        const row = {};
        columns.forEach((col, idx) => row[col] = parts[idx] || '');
        return row;
      });
    },
    renderPreview: function (tableEl, columns, rows) {
      if (!tableEl) return;
      const thead = tableEl.querySelector('thead');
      const tbody = tableEl.querySelector('tbody');
      thead.innerHTML = '<tr>' + columns.map(c => '<th>' + escapeHtml(c) + '</th>').join('') + '<th class="text-end">Actions</th></tr>';
      tbody.innerHTML = rows.map((row, index) => {
        const tds = columns.map(c => '<td>' + escapeHtml(row[c] || '') + '</td>').join('');
        return '<tr data-index="' + index + '">' + tds + '<td class="text-end"><button type="button" class="btn btn-sm btn-outline-primary js-edit-row me-1">Edit</button><button type="button" class="btn btn-sm btn-outline-danger js-delete-row">Delete</button></td></tr>';
      }).join('');
    },
    exportPDF: function (elementId, filename) {
      if (typeof window.jspdf === 'undefined') {
        alert('PDF export library is loading from CDN. Please connect to internet or use browser print as a fallback.');
        return;
      }
      const el = document.getElementById(elementId);
      html2canvas(el, { scale: 2, backgroundColor: '#ffffff' }).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const pdf = new window.jspdf.jsPDF('p', 'mm', 'a4');
        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = (canvas.height * pdfWidth) / canvas.width;
        pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
        pdf.save(filename + '.pdf');
      });
    },
    exportPNG: function (elementId, filename) {
      const el = document.getElementById(elementId);
      html2canvas(el, { scale: 2, backgroundColor: '#ffffff' }).then(canvas => {
        const link = document.createElement('a');
        link.download = filename + '.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
      });
    },
    exportDOC: function (elementId, filename) {
      const el = document.getElementById(elementId);
      const html = '<html><head><meta charset="utf-8"><title>' + filename + '</title></head><body>' + el.outerHTML + '</body></html>';
      const blob = new Blob(['\ufeff', html], { type: 'application/msword' });
      const link = document.createElement('a');
      link.download = filename + '.doc';
      link.href = URL.createObjectURL(blob);
      link.click();
    }
  };

  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  document.addEventListener('DOMContentLoaded', function () {
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'bright';
    setTheme(currentTheme);

    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
      themeToggle.addEventListener('click', function () {
        const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'bright' : 'dark';
        setTheme(next);
      });
    }

    document.querySelectorAll('.counter').forEach(function (el) {
      const target = parseInt(el.getAttribute('data-target') || '0', 10);
      let current = 0;
      const steps = 40;
      const inc = Math.max(1, Math.ceil(target / steps));
      const timer = setInterval(function () {
        current += inc;
        if (current >= target) {
          current = target;
          clearInterval(timer);
        }
        el.textContent = current;
      }, 18);
    });

    const canvas = document.getElementById('heroCanvas');
    if (canvas) {
      const ctx = canvas.getContext('2d');
      let w, h, particles;
      function resize() {
        const rect = canvas.getBoundingClientRect();
        w = canvas.width = rect.width * devicePixelRatio;
        h = canvas.height = rect.height * devicePixelRatio;
        particles = Array.from({ length: 24 }, () => ({
          x: Math.random() * w,
          y: Math.random() * h,
          r: 1.2 + Math.random() * 2.5,
          dx: (-0.45 + Math.random()) * 0.8 * devicePixelRatio,
          dy: (-0.45 + Math.random()) * 0.8 * devicePixelRatio
        }));
      }
      function draw() {
        ctx.clearRect(0, 0, w, h);
        const grad = ctx.createLinearGradient(0, 0, w, h);
        grad.addColorStop(0, 'rgba(255,255,255,0.08)');
        grad.addColorStop(1, 'rgba(255,255,255,0.02)');
        ctx.fillStyle = grad;
        ctx.fillRect(0, 0, w, h);
        particles.forEach(function (p) {
          p.x += p.dx; p.y += p.dy;
          if (p.x < 0 || p.x > w) p.dx *= -1;
          if (p.y < 0 || p.y > h) p.dy *= -1;
          ctx.beginPath();
          ctx.arc(p.x, p.y, p.r * devicePixelRatio * 0.6, 0, Math.PI * 2);
          ctx.fillStyle = 'rgba(255,255,255,.72)';
          ctx.fill();
        });
        requestAnimationFrame(draw);
      }
      resize();
      window.addEventListener('resize', resize);
      requestAnimationFrame(draw);
    }
  });
})();
