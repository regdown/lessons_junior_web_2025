"use client";

import React, { createContext, useContext, useState } from 'react';

interface CounterContextValue {
    count: number;
    increment: () => void;
}

const CounterContext = createContext<CounterContextValue | undefined>(undefined);

export const CounterProvider: React.FC<{ children: React.ReactNode }> = ({children }) => {
    const [count, setCount] = useState(0);
    const increment = () => setCount(prev => prev + 1);
    const value: CounterContextValue = { count, increment };
    return (
        <CounterContext.Provider value={value}>
            {children}
        </CounterContext.Provider>
    );
};

export function useCounter() {
    const context = useContext(CounterContext);
    if (context === undefined) {
        throw new Error("useCounter must be used within a CounterProvider");
    }
    return context;
}
