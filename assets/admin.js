(function () {
  const root = document.documentElement;
  const toggle = document.getElementById('admin-theme-toggle');

  function applyTheme(theme) {
    root.classList.remove('light', 'dark');
    root.classList.add(theme);
    localStorage.setItem('theme', theme);

    if (toggle) {
      toggle.textContent = theme === 'dark' ? 'Light Mode' : 'Dark Mode';
    }
  }

  const storedTheme = localStorage.getItem('theme') || 'light';
  applyTheme(storedTheme);

  if (toggle) {
    toggle.addEventListener('click', function () {
      const nextTheme = root.classList.contains('dark') ? 'light' : 'dark';
      applyTheme(nextTheme);
    });
  }
})();
