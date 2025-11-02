import { useState } from 'react';
export function Counter() {
    // Инициализируем состояние count значением 0
    const [count, setCount] = useState(0);
    
    return (
        <div>
        <p>Счетчик: {count}</p>
        <button onClick={() => setCount(count + 1)}>Увеличить</button>
        </div>
    );
}
