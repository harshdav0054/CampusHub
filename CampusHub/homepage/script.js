document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.querySelector('.search-input');
  const noResultsMsg = document.getElementById('noResults');
  const collegeCards = document.querySelectorAll('.college-card');

  function checkNoResults() {
    let matchCount = 0;
    collegeCards.forEach(card => {
      if (card.style.display !== 'none') matchCount++;
    });
    noResultsMsg.style.display = matchCount === 0 ? 'block' : 'none';
  }

  // Initial check
  checkNoResults();

  if (searchInput) {
    searchInput.addEventListener('input', function () {
      const searchTerm = searchInput.value.toLowerCase().trim();
      collegeCards.forEach(card => {
        const name = card.querySelector('h4').textContent.toLowerCase();
        const location = card.querySelector('.location').textContent.toLowerCase();
        const desc = card.querySelector('.desc').textContent.toLowerCase();
        const courses = card.querySelector('.info span').textContent.toLowerCase();

        const matches = name.includes(searchTerm) ||
                        location.includes(searchTerm) ||
                        desc.includes(searchTerm) ||
                        courses.includes(searchTerm);

        card.style.display = matches ? 'grid' : 'none';
      });
      checkNoResults();
    });
  }
});
