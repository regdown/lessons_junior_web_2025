// script.js
function loadProducts() {
  fetch('/api/products')
    .then(response => response.json())
    .then(products => {
      const listDiv = document.getElementById('product-list');
      listDiv.innerHTML = "";  // очищаем (на случай повторного вызова)
      products.forEach(prod => {
        // Создаем элемент для товара
        const prodDiv = document.createElement('div');
        prodDiv.className = 'product';
        prodDiv.innerHTML = `
          <h2>${prod.name}</h2>
          <p>${prod.description || ''}</p>
          <p class="price">Цена: ${prod.price} ₽</p>
        `;
        listDiv.appendChild(prodDiv);
      });
    })
    .catch(error => {
      console.error('Error loading products:', error);
      const listDiv = document.getElementById('product-list');
      listDiv.innerHTML = "<p style='color:red;'>Ошибка загрузки данных</p>";
    });
}

// Загружаем товары при открытии страницы
window.addEventListener('DOMContentLoaded', loadProducts);
