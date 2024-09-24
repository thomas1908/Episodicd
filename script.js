const toggleMenu = document.querySelector('.toggle-menu');
const subnav = document.querySelector('.subnav');

toggleMenu.addEventListener('mouseover', () => {
    subnav.style.display = 'block';
});

toggleMenu.addEventListener('mouseleave', () => {
    subnav.style.display = 'none';
});

// Si vous souhaitez que le menu reste ouvert quand vous survolez, vous pouvez gérer cela avec des événements supplémentaires
subnav.addEventListener('mouseenter', () => {
    subnav.style.display = 'block';
});

subnav.addEventListener('mouseleave', () => {
    subnav.style.display = 'none';
});
