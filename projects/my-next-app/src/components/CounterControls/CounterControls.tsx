"use client";

import { useCounter } from "../CounterContext/CounterContext";

export default function CounterControls() {
    const { count, increment } = useCounter();
    return (
        <div style={{ padding: '10px', border: '1px solid #ccc', marginTop: '20px' }}>
            <p>Глобальный счётчик: <strong>{count}</strong></p>
            <button onClick={increment}>+1</button>
        </div>
    );
}
