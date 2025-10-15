document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.querySelector('.search-input');
  const noResultsMsg = document.getElementById('noResults');

  if (!searchInput || !noResultsMsg) return;

  searchInput.addEventListener('input', function () {
    const searchTerm = searchInput.value.toLowerCase().trim();
    const collegeCards = document.querySelectorAll('.college-card');
    let matchCount = 0;

    collegeCards.forEach(card => {
      const name = card.querySelector('h4').textContent.toLowerCase();
      const location = card.querySelector('.location').textContent.toLowerCase();
      const desc = card.querySelector('.desc').textContent.toLowerCase();
      const courses = card.querySelector('.info span').textContent.toLowerCase();

      const matches =
        name.includes(searchTerm) ||
        location.includes(searchTerm) ||
        desc.includes(searchTerm) ||
        courses.includes(searchTerm);

      card.style.display = matches ? 'grid' : 'none';
      if (matches) matchCount++;
    });

    noResultsMsg.style.display = matchCount === 0 ? 'block' : 'none';
  });
});
