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