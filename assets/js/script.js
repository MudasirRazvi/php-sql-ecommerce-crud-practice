function toggleDarkMode() {
    const body = document.body;

    if (body.classList.contains('dark-mode')) {
        body.classList.remove('dark-mode');
        body.classList.add('light-mode');
        document.cookie = "theme=light-mode; path=/; max-age=" + 60*60*24*30;
    } else {
        body.classList.remove('light-mode');
        body.classList.add('dark-mode');
        document.cookie = "theme=dark-mode; path=/; max-age=" + 60*60*24*30;
    }
}
