import { useState, useCallback } from 'react';
import { RegisterForm } from './RegisterForm';

// Кастомный хук: управляет булевым значением с функцией переключения
function useToggle(initialValue: boolean = false): [boolean, () => void] {
    const [value, setValue] = useState(initialValue);

    // мемоизируем функцию toggle, чтобы она не создавалась заново по ненадобности
    const toggleValue = useCallback(() => {
        setValue(v => !v);
    }, []);
    return [value, toggleValue];
}

// Пример использования хука useToggle в компоненте
export const Menu: React.FC = () => {
    const [isOpen, toggleOpen] = useToggle(false); // используем кастомный хук
    return (
        <div>
            <button onClick={toggleOpen}>
                {isOpen ? 'Закрыть меню' : 'Открыть меню'}
            </button>
            {isOpen && <nav> ... меню ... </nav>}
            {isOpen && <RegisterForm />}
        </div>
    );
};
