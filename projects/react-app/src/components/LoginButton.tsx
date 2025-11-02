import React from 'react';

interface LoginButtonProps {
    onLogin: () => void; // пропсы могут передавать и функции
}

export function LoginButton({ onLogin }: LoginButtonProps) {
    // Обработчик клика
    function handleClick(event: React.MouseEvent<HTMLButtonElement>) {
        console.log('Clicked button:', event.currentTarget);
        onLogin(); // вызываем переданный колбэк
    }
    
    return <button onClick={handleClick}>Войти</button>;
}
