"use client"; // делает весь компонент клиентским (hooks разрешены)

import { useState, FormEvent } from 'react';

export default function SubscribeForm() {
    const [email, setEmail] = useState<string>(""); // состояние для email
    const [submitted, setSubmitted] = useState<boolean>(false); // отправлена ли форма
    const handleSubmit = (event: FormEvent) => {
        event.preventDefault(); // предотвращаем перезагрузку страницы
        if (email) {
            setSubmitted(true);
            // Здесь можно сделать что-то с email, например отправить на сервер
            console.log("Submitted email:", email);
        }
    };
    return (
        <form onSubmit={handleSubmit} style={{ marginTop: '20px' }}>
            {!submitted ? (
                <>
                    <label>
                        Введите email:
                        <input
                        type="email"
                        value={email}
                        onChange={e => setEmail(e.target.value)}
                        required
                        />
                    </label>
                    <button type="submit">Подписаться</button>
                </>
            ) : (
                <p>Спасибо! Вы подписаны: {email}</p>
            )}
        </form>
    );
}