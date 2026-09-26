// OpenRTMP.org — site behaviour

document.addEventListener('DOMContentLoaded', () => {
  initNavToggle();
  initCopyButtons();
  initActiveDocsLink();
  initYear();
});

function initNavToggle() {
  const toggle = document.querySelector('.nav-toggle');
  const nav = document.querySelector('.nav');
  if (!toggle || !nav) return;

  toggle.addEventListener('click', () => {
    nav.classList.toggle('open');
    toggle.setAttribute('aria-expanded', String(nav.classList.contains('open')));
  });
}

const COPY_LABELS = {
  en: { copy: 'Copy', copied: 'Copied!', error: 'Error' },
  de: { copy: 'Kopieren', copied: 'Kopiert!', error: 'Fehler' },
};

function initCopyButtons() {
  const labels = COPY_LABELS[document.documentElement.lang] || COPY_LABELS.en;
  document.querySelectorAll('pre').forEach((pre) => {
    const btn = document.createElement('button');
    btn.className = 'copy-btn';
    btn.type = 'button';
    btn.textContent = labels.copy;
    pre.style.position = pre.style.position || 'relative';
    pre.appendChild(btn);

    btn.addEventListener('click', async () => {
      const code = pre.querySelector('code') || pre;
      const text = code.innerText;
      try {
        await navigator.clipboard.writeText(text);
        btn.textContent = labels.copied;
      } catch (err) {
        console.error('Failed to copy code to clipboard:', err);
        btn.textContent = labels.error;
      }
      setTimeout(() => { btn.textContent = labels.copy; }, 1500);
    });
  });
}

function initActiveDocsLink() {
  const links = document.querySelectorAll('.docs-nav a');
  if (!links.length) return;

  const sections = Array.from(links)
    .map((a) => a.getAttribute('href'))
    .filter((href) => href?.startsWith('#'))
    .map((href) => document.querySelector(href))
    .filter(Boolean);

  if (!sections.length) return;

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          links.forEach((a) => a.classList.remove('active'));
          const active = document.querySelector(`.docs-nav a[href="#${entry.target.id}"]`);
          if (active) active.classList.add('active');
        }
      });
    },
    { rootMargin: '-40% 0px -50% 0px' }
  );

  sections.forEach((section) => observer.observe(section));
}

function initYear() {
  const el = document.getElementById('year');
  if (el) el.textContent = new Date().getFullYear();
}
