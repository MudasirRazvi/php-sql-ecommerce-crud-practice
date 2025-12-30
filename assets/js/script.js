function toggleDarkMode() {
    const body = document.body;
    if (body.classList.contains('dark-mode')) {
        body.classList.remove('dark-mode');
        document.cookie = "theme=light;path=/";
    } else {
        body.classList.add('dark-mode');
        document.cookie = "theme=dark;path=/";
    }
}

window.onload = function () {
    const theme = document.cookie.split('; ').find(row => row.startsWith('theme='));
    if (theme && theme.split('=')[1] === 'dark') {
        document.body.classList.add('dark-mode');
    }
};

// Function to show the popup
function showPopup() {
    document.getElementById('promoPopup').style.display = 'flex';
}

function closePopup() {
    document.getElementById('promoPopup').style.display = 'none';
}

window.addEventListener('load', function() {
    setTimeout(showPopup, 5000);
});

window.onclick = function(event) {
    let modal = document.getElementById('promoPopup');
    if (event.target == modal) {
        closePopup();
    }
}