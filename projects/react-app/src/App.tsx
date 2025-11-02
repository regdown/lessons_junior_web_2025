import React, { useState } from 'react';
import './App.css';
import { Demo } from './components/Demo';
import { HelloMessage } from './components/HelloMessage';
import { Greeting } from './components/Greeting';
import { Counter } from './components/Counter';
import { CounterTitle } from './components/CounterTitle';
import { Button } from './components/Button';
import { LoginButton } from './components/LoginButton';
import { FruitsList } from './components/FruitsList';
import { UserInfo } from './components/UserInfo';
import { BrowserRouter, Routes, Route, Link } from 'react-router-dom';
import { Header } from './components/Header';


function App() {
  const [userLogged, setUserLogged] = useState(false);
  const login = () => {
    setUserLogged(true);     // ✅ Меняем состояние
    alert("Авторизация...");
  };

  return (
    <div>
      <Header />
      <BrowserRouter>
        {/* Навигационное меню */}
        <nav style={{ marginBottom: '1em' }}>
          <Link to="/">Home</Link> | <Link to="/about">About</Link>
        </nav>
        {/* Определение маршрутов */}
        <Routes>
          <Route path="/" element={<Greeting name="Дома" id="Test" />} />
          <Route path="/about" element={<Greeting name="О программе" />} />
        </Routes>
      </BrowserRouter>
      {/* Используем наш компонент внутри JSX */}
      <Demo />
      <HelloMessage />
      <Greeting name="Алиса" />
      <Greeting name="Боб" />
      <Counter />
      <CounterTitle />
      <Button text='123' />
      <br />
      <LoginButton onLogin={login} />      
      <UserInfo isLoggedIn={userLogged} />
      <FruitsList />
    </div>
  );
}

export default App;
