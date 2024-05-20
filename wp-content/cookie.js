function setCookie(cookieName, cookieValue, expirationDays) {
    const d = new Date();
    d.setTime(d.getTime() + (expirationDays * 24 * 60 * 60 * 1000));
    const expires = "expires=" + d.toUTCString();
    document.cookie = cookieName + "=" + cookieValue + ";" + expires + ";path=/";
}

function deleteCookie(cookieName) {
    const d = new Date();
    d.setTime(d.getTime() - 1000); 
    const expires = "expires=" + d.toUTCString();
    document.cookie = cookieName + "=; " + expires + ";path=/";
}


setCookie("userName", "Jithin", 30); 
setCookie("lastName", "George", 30);

