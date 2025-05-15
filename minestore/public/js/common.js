const menuBurger = document.querySelector('.js-burgerMenu');
const burger = document.querySelector('.js-burger');
const menuList = document.querySelector('.js-menuList');

const sidebarDropdownButton = document.querySelector('.js-sidebarDropdownButton');
const sidebarDropdownList = document.querySelector('.js-sidebarDropdownList');

const toggleAsideDropdownMenu = () => {
	sidebarDropdownList.classList.toggle('hidden');
};

const toggleMobileMenu = () => {
	menuList.classList.toggle('hidden');
	burger.classList.toggle('burger-active'); 
};

if (menuBurger !== null) menuBurger.addEventListener('click', toggleMobileMenu);
if (sidebarDropdownButton !== null) sidebarDropdownButton.addEventListener('click', toggleAsideDropdownMenu);

if (typeof toastr !== "undefined") toastr.options.closeDuration = 10000;