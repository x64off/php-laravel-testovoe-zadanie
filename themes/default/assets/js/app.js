document.addEventListener('DOMContentLoaded', function() {
  const themeSwitch = document.getElementById('theme-switch');
  const root = document.documentElement;

  // Проверяем значение в localStorage при загрузке страницы
  if (localStorage.getItem('theme') === 'dark') {
    root.classList.add('dark-theme');
    themeSwitch.classList.add('active');
  }

  themeSwitch.addEventListener('click', () => {
    root.classList.toggle('dark-theme');

    // Проверяем текущую тему и сохраняем ее в localStorage
    if (root.classList.contains('dark-theme')) {
      themeSwitch.classList.add('active');
      localStorage.setItem('theme', 'dark');
    } else {
      themeSwitch.classList.remove('active');
      localStorage.setItem('theme', 'light');
    }
  });

const openModalBtn = document.getElementById('open-signup');
const modal = document.getElementById('signup-modal');
const closeModalBtn = document.querySelector('.close');
if (openModalBtn) {
  openModalBtn.addEventListener('click', () => {
    modal.style.display = 'block';
  });
  
  closeModalBtn.addEventListener('click', () => {
    modal.style.display = 'none';
  });
  
  
}
window.addEventListener('click', (event) => {
  if (event.target === modal) {
    modal.style.display = 'none';
  }
});

const changeSignupBtn = document.getElementById('change-signup');
const form1 = document.getElementById('login-form');
const form2 = document.getElementById('register-form');

changeSignupBtn.addEventListener('click', () => {
  if (form1.style.display === 'none') {
    form1.style.display = 'block';
    form2.style.display = 'none';
    changeSignupBtn.textContent = 'Регистрация';
  } else {
    form1.style.display = 'none';
    form2.style.display = 'block';
    changeSignupBtn.textContent = 'Войти';
  }
});

const userMenuButton = document.getElementById('user-menu-button');
const userMenu = document.getElementById('user-menu');
if(userMenuButton){
  userMenuButton.addEventListener('click', () => {
    userMenu.style.display = (userMenu.style.display === 'none') ? 'block' : 'none';
  });
}


});
const menuItems = document.querySelectorAll('li.menu');
    menuItems.forEach(item => {
      item.addEventListener('click', () => {
        // Удаление класса "active" у других элементов
        menuItems.forEach(otherItem => {
          if (otherItem !== item) {
            otherItem.classList.remove('active');
          }
        });

        // Добавление или удаление класса "active" для текущего элемента
        item.classList.toggle('active');
      });
    });
    function loadContent($page) {
        fetch('/get/'+$page) // Здесь указывается URL страницы с контентом
          .then(response => response.text())
          .then(data => {
            const container = document.getElementById('container');
            container.innerHTML = data;
          })
          .catch(error => {
            console.log('Ошибка при загрузке контента:', error);
          });
      }

      
      