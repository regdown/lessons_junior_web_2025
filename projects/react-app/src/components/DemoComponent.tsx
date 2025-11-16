import React, { useState, useMemo, useCallback } from 'react';

// Функция имитирует дорогостоящую операцию (например, вычисление n-го числа Фибоначчи)
function heavyCompute(num: number): number {
    console.log('Computing...');
    // (здесь могла бы быть сложная логика, напр. вычисление за O(n^2))
    let result = 0;
    for (let i = 0; i < 10000000; i++) { 
        result += Math.sin(num) * Math.cos(i); 
    }
    return result;
}

export const DemoComponent: React.FC = () => {
    const [count, setCount] = useState(0);
    const [value, setValue] = useState(42);

    // Мемоизация тяжёлого вычисления: пересчитываем только если изменился value
    const expensiveValue = useMemo(() => heavyCompute(value), [value]);

    // Мемоизация функции: создаётся один раз и не пересоздаётся при каждом рендере (зависимость пуста)
    const incrementCount = useCallback(() => {
        setCount(prev => prev + 1);
    }, []);

    return (
        <div>
            <p>Результат вычисления: <b>{expensiveValue}</b></p>
            <p>Счетчик: {count}</p>
            <button onClick={incrementCount}>+1</button>
        </div>
    );
};
