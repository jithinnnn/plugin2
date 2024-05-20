const acceptedCookies = {};

function setCookie(cookieName, cookieValue, expirationDays) {
    const d = new Date();
    d.setTime(d.getTime() + (expirationDays * 24 * 60 * 60 * 1000));
    const expires = "expires=" + d.toUTCString();
    document.cookie = `${cookieName}=${cookieValue};${expires};path=/`;

    acceptedCookies[cookieName] = expirationDays;
}

function deleteCookie(cookieName) {
    const d = new Date();
    d.setTime(d.getTime() - 1000);
    const expires = "expires=" + d.toUTCString();
    document.cookie = `${cookieName}=; ${expires}; path=/`;

    delete acceptedCookies[cookieName];
}

function getCookie(cookieName) {
    const name = `${cookieName}=`;
    const decodedCookie = decodeURIComponent(document.cookie);
    const ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i].trim();
        if (c.indexOf(name) === 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

setCookie("userName", "Jithin", 30);
setCookie("lastName", "George", 30);
