import { FC } from 'react';
interface ButtonProps { text: string; }
export const Button: FC<ButtonProps> = ({ text }) => <button>{text}</button>;