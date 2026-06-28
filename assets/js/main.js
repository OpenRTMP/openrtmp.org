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
  });
}

function initCopyButtons() {
  document.querySelectorAll('pre').forEach((pre) => {
    const btn = document.createElement('button');
    btn.className = 'copy-btn';
    btn.type = 'button';
    btn.textContent = 'Copy';
    pre.style.position = pre.style.position || 'relative';
    pre.appendChild(btn);

    btn.addEventListener('click', async () => {
      const code = pre.querySelector('code') || pre;
      const text = code.innerText;
      try {
        await navigator.clipboard.writeText(text);
        btn.textContent = 'Copied!';
      } catch (err) {
        btn.textContent = 'Error';
      }
      setTimeout(() => { btn.textContent = 'Copy'; }, 1500);
    });
  });
}

function initActiveDocsLink() {
  const links = document.querySelectorAll('.docs-nav a');
  if (!links.length) return;

  const sections = Array.from(links)
    .map((a) => document.querySelector(a.getAttribute('href')))
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
