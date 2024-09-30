const searchButton = document.getElementById('search-btn');
const searchInput = document.getElementById('search-input');
const searchIcon = document.getElementById('search-icon');
const closeIcon = document.getElementById('close-icon');

searchButton.addEventListener('click', () => {
    searchInput.classList.toggle('visible');

    if (searchInput.classList.contains('visible')) {
        searchInput.focus();
        searchIcon.style.display = 'none';
        closeIcon.style.display = 'block';
        
    } else {
        searchIcon.style.display = 'block';
        closeIcon.style.display = 'none';
    }
});

document.getElementById('search-btn').addEventListener('click', function() {
    const searchBarContainer = document.querySelector('.search-bar-container');
    searchBarContainer.classList.toggle('open');
});