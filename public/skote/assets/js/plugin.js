!function () {
    "use strict";

    if (window.sessionStorage) {
        var t = sessionStorage.getItem("is_visited");

        if (t) switch (t) {

            case "light-mode-switch":
                document.documentElement.removeAttribute("dir");

                if (document.getElementById("bootstrap-style").getAttribute("href") !== window.BASE_URL + "assets/css/bootstrap.min.css") {
                    document.getElementById("bootstrap-style")
                        .setAttribute("href", window.BASE_URL + "assets/css/bootstrap.min.css");
                }

                if (document.getElementById("app-style").getAttribute("href") !== window.BASE_URL + "assets/css/app.min.css") {
                    document.getElementById("app-style")
                        .setAttribute("href", window.BASE_URL + "assets/css/app.min.css");
                }

                document.documentElement.setAttribute("data-bs-theme", "light");
                break;

            case "dark-mode-switch":
                document.documentElement.removeAttribute("dir");

                if (document.getElementById("bootstrap-style").getAttribute("href") !== window.BASE_URL + "assets/css/bootstrap.min.css") {
                    document.getElementById("bootstrap-style")
                        .setAttribute("href", window.BASE_URL + "assets/css/bootstrap.min.css");
                }

                if (document.getElementById("app-style").getAttribute("href") !== window.BASE_URL + "assets/css/app.min.css") {
                    document.getElementById("app-style")
                        .setAttribute("href", window.BASE_URL + "assets/css/app.min.css");
                }

                document.documentElement.setAttribute("data-bs-theme", "dark");
                break;

            case "rtl-mode-switch":
                if (document.getElementById("bootstrap-style").getAttribute("href") !== window.BASE_URL + "assets/css/bootstrap-rtl.min.css") {
                    document.getElementById("bootstrap-style")
                        .setAttribute("href", window.BASE_URL + "assets/css/bootstrap-rtl.min.css");
                }

                if (document.getElementById("app-style").getAttribute("href") !== window.BASE_URL + "assets/css/app-rtl.min.css") {
                    document.getElementById("app-style")
                        .setAttribute("href", window.BASE_URL + "assets/css/app-rtl.min.css");
                }

                document.documentElement.setAttribute("dir", "rtl");
                document.documentElement.setAttribute("data-bs-theme", "light");
                break;

            case "dark-rtl-mode-switch":
                if (document.getElementById("bootstrap-style").getAttribute("href") !== window.BASE_URL + "assets/css/bootstrap-rtl.min.css") {
                    document.getElementById("bootstrap-style")
                        .setAttribute("href", window.BASE_URL + "assets/css/bootstrap-rtl.min.css");
                }

                if (document.getElementById("app-style").getAttribute("href") !== window.BASE_URL + "assets/css/app-rtl.min.css") {
                    document.getElementById("app-style")
                        .setAttribute("href", window.BASE_URL + "assets/css/app-rtl.min.css");
                }

                document.documentElement.setAttribute("dir", "rtl");
                document.documentElement.setAttribute("data-bs-theme", "dark");
                break;

            default:
                console.log("Something wrong with the layout mode.");
        }
    }
}(window.jQuery);
