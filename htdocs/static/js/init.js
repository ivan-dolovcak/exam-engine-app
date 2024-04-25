function getCookie(cookieName)
{
    const cookies = document.cookie.split(";");
    for (const cookie of cookies)
        if (cookie.split("=")[0].trim() === cookieName)
            return decodeURIComponent(cookie.split("=")[1]);

    return null;
}

// Set theme:
const preferences = JSON.parse(getCookie("examEnginePreferences"));
const themeClassName = `${preferences.theme}-theme`;
document.documentElement.classList.add(themeClassName);

document.documentElement.lang = preferences.lang;

// Highlight current page anchor in nav:
const currentPage = location.pathname;
const a = document.querySelector(`nav a[href="${currentPage}"]`);
if (a)
    a.classList.add("active");
