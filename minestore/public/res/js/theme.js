'use strict';

(function () {
    $(".style-switcher-toggle").on('click', function(e){
        // e.preventDefault();
        var iStyleSwitcher = $(this).find('i');
        var isLight = true;
        if($('html').hasClass('light-style')){
            isLight = false;
            iStyleSwitcher.removeClass('bx-sun');
            iStyleSwitcher.addClass('bx-moon');
            document.documentElement.classList.remove("light-style");
            document.documentElement.classList.add("dark-style");
            document.documentElement.setAttribute("data-template", "vertical-menu-theme-default-dark");
            document.querySelector(".template-customizer-core-css").href = "/res/vendor/css/core-dark.css";
            document.querySelector(".template-customizer-theme-css").href = "/res/vendor/css/theme-default-dark.css";
            document.querySelector(".app-brand img.app-brand-logo").src = "/res/img/logo-white.png";
        } else {
            isLight = true;
            iStyleSwitcher.removeClass('bx-moon');
            iStyleSwitcher.addClass('bx-sun');
            document.documentElement.classList.remove("dark-style");
            document.documentElement.classList.add("light-style");
            document.documentElement.setAttribute("data-template", "vertical-menu-theme-default-light");
            document.querySelector(".template-customizer-core-css").href = "/res/vendor/css/core.css";
            document.querySelector(".template-customizer-theme-css").href = "/res/vendor/css/theme-default.css";
            document.querySelector(".app-brand img.app-brand-logo").src = "/res/img/logo-colored.png";
        }
        $.ajax({
            method: "GET",
            url: "/admin/themeStyle?style=" + (isLight ? 'light' : 'dark'),
            data: {},
        });
    });
})();