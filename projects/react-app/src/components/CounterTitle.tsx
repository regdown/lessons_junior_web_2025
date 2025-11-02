import { useState, useEffect } from 'react';

export function CounterTitle() {
    const [count, setCount] = useState(0);
    // Побочный эффект: обновить заголовок окна при изменении count
    useEffect(() => {
        document.title = `Count: ${count}`;
    }, [count]); // срабатывает только когда меняется count

    return (
        <div>
            <p>Счетчик: {count}</p>
            <button onClick={() => setCount(count + 1)}>Увеличить</button>
        </div>
    );
}

