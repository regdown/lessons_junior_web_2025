// Базовый адрес API (тот же домен/порт)
const API = window.location.origin;

// Ссылки на элементы
const els = {
    grid: document.getElementById('grid'),
    empty: document.getElementById('empty'),
    search: document.getElementById('search'),
    sort: document.getElementById('sort'),
    cats: document.querySelector('.categories'),
};

// Состояние
const state = {
    products: [],
    categories: [],
    activeCategory: '',
    search: '',
    sort: 'relevance',
};

// Плейсхолдер для картинок
function placeholderDataURI(title = 'Нет изображения') {
    const svg = encodeURIComponent(
        `<svg xmlns='http://www.w3.org/2000/svg' width='800' height='600'>
      <rect fill='#e5e7eb' width='100%' height='100%'/>
      <text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle'
        font-family='Arial' font-size='24' fill='#6b7280'>${title}</text>
    </svg>`
    );
    return `data:image/svg+xml;charset=utf-8,${svg}`;
}

/* ----------- Загрузка данных ----------- */
async function fetchJSON(url) {
    const res = await fetch(url);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
}

async function loadCategories() {
    try {
        const data = await fetchJSON(`${API}/api/categories`);
        state.categories = Array.isArray(data) ? data : [];
        renderCategories();
    } catch (e) {
        console.error('categories:', e);
        state.categories = [];
        renderCategories(); // покажем только «Все»
    }
}

async function loadProducts(categoryId = '') {
    const url = new URL(`${API}/api/products`);
    if (categoryId) url.searchParams.set('category_id', categoryId);

    showSkeletons();
    try {
        const data = await fetchJSON(url);
        state.products = Array.isArray(data) ? data : [];
        applyFilters();
    } catch (e) {
        console.error('products:', e);
        state.products = [];
        applyFilters();
    }
}

/* ----------- Рендеринг ----------- */
function renderCategories() {
    els.cats.innerHTML = '';
    const all = createChip('Все', '');
    all.classList.add('active');
    els.cats.appendChild(all);

    state.categories.forEach(c => {
        els.cats.appendChild(createChip(c.name ?? `Категория ${c.id}`, String(c.id)));
    });
}

function createChip(text, id) {
    const b = document.createElement('button');
    b.className = 'chip';
    b.textContent = text;
    b.dataset.id = id;
    b.addEventListener('click', () => {
        [...els.cats.querySelectorAll('.chip')].forEach(x => x.classList.remove('active'));
        b.classList.add('active');
        state.activeCategory = id;
        loadProducts(id);
    });
    return b;
}

function showSkeletons(count = 8) {
    els.empty.classList.add('hidden'); // используем CSS-класс .hidden { display:none }
    els.grid.innerHTML = '';
    for (let i = 0; i < count; i++) {
        const sk = document.createElement('div');
        sk.className = 'card sk';
        sk.innerHTML = `
      <div class="thumb skeleton"></div>
      <div class="line w90 skeleton"></div>
      <div class="line w60 skeleton"></div>
      <div class="line w90 skeleton"></div>
    `;
        els.grid.appendChild(sk);
    }
}

function renderGrid(items) {
    els.grid.innerHTML = '';
    if (!items.length) {
        els.empty.classList.remove('hidden'); // показываем блок «ничего не найдено»
        return;
    }
    els.empty.classList.add('hidden');

    items.forEach(p => {
        const card = document.createElement('article');
        card.className = 'card';
        const imgSrc = p.image_url ? p.image_url : placeholderDataURI(p.name || 'Товар');
        const price = (p.price ?? 0) + ' ₽';

        card.innerHTML = `
      <img class="thumb" alt="${p.name ?? 'Товар'}" src="${imgSrc}">
      <div class="content">
        <h3 title="${p.name ?? ''}">${p.name ?? ''}</h3>
        <p>${p.description ?? ''}</p>
        <div class="row">
          <div class="price">${price}</div>
          <button class="btn" type="button">В корзину</button>
        </div>
      </div>
    `;
        els.grid.appendChild(card);
    });
}

/* ----------- Фильтры ----------- */
function applyFilters() {
    const q = state.search.trim().toLowerCase();
    let arr = state.products.slice();

    if (q) {
        arr = arr.filter(p => (p.name ?? '').toLowerCase().includes(q));
    }

    switch (state.sort) {
        case 'price_asc':
            arr.sort((a, b) => (+a.price || 0) - (+b.price || 0));
            break;
        case 'price_desc':
            arr.sort((a, b) => (+b.price || 0) - (+a.price || 0));
            break;
        case 'name_asc':
            arr.sort((a, b) => String(a.name || '').localeCompare(String(b.name || ''), 'ru'));
            break;
        case 'name_desc':
            arr.sort((a, b) => String(b.name || '').localeCompare(String(a.name || ''), 'ru'));
            break;
        default:
            // relevance — без сортировки
            break;
    }

    renderGrid(arr);
}

/* ----------- События ----------- */
els.search.addEventListener('input', (e) => {
    state.search = e.target.value;
    applyFilters();
});

els.sort.addEventListener('change', (e) => {
    state.sort = e.target.value;
    applyFilters();
});

/* ----------- Инициализация ----------- */
window.addEventListener('DOMContentLoaded', async () => {
    await loadCategories();
    await loadProducts('');
});
