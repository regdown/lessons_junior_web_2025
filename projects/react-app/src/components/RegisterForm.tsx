import React, { useState, ChangeEvent, FormEvent } from 'react';

interface FormData {
    username: string;
    email: string;
}

export const RegisterForm: React.FC = () => {
    // Храним значения полей формы в состоянии
    const [form, setForm] = useState<FormData>({ username: '', email: '' });
    // Обработчик изменения поля – обновляем соответствующее свойство в состоянии
    const handleChange = (e: ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setForm(prevForm => ({ ...prevForm, [name]: value })); // благодаря имени поля меняем нужное свойство
};

// Обработчик отправки формы
const handleSubmit = (e: FormEvent) => {
    e.preventDefault(); // отменяем перезагрузку страницы
    console.log('Отправка формы:', form);
    // Здесь можно выполнить валидацию и отправить данные `form` на сервер через fetch/Axios
};

return (
<form onSubmit={handleSubmit}>
    <div>
        <label>
        Имя пользователя:
        <input
        name="username"
        type="text"
        value={form.username}
        onChange={handleChange}
        />
        </label>
    </div>
    <div>
        <label>
        Email:
        <input
        name="email"
        type="email"
        value={form.email}
        onChange={handleChange}
        />
        </label>
    </div>
    <button type="submit">Регистрация</button>
</form>
);
};
