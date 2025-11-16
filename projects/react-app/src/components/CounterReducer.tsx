import { useReducer } from 'react';

type CounterAction = { type: 'increment' } | { type: 'decrement' }

const counterReducer = (state: number, action: CounterAction): number => {
    switch (action.type) {
        case 'increment':
        return state + 1;
        case 'decrement':
        return state - 1;
        default:
        return state; // на случай неизвестного действия возвращаем текущее
    }
};

export function CounterReducer() {
    const [count, dispatch] = useReducer(counterReducer, 0);
    return (
        <div>
            <p>Значение: {count}</p>
            {/* 4. Вызываем dispatch с объектом действия при клике */}
            <button onClick={() => dispatch({ type: 'decrement' })}>-1</button>
            <button onClick={() => dispatch({ type: 'increment' })}>+1</button>
        </div>
    );
}
