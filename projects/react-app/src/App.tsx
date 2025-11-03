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
import { ButtonColored } from './components/ButtonColored';
import { ButtonHint } from './components/ButtonHint';
import { UserAge } from './components/UserAge';

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
      <ButtonColored text='123' color='red'/>
      <ButtonHint text='123456' color='red' isHint={true}/>
      <br />
      <LoginButton onLogin={login} />      
      <UserInfo isLoggedIn={userLogged} />
      <FruitsList />
      <br/>
      <UserAge age={19} />
    </div>
  );
}

export default App;
