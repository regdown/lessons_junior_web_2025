import { FC } from 'react';
import { ButtonProps } from '../interfaces/ButtonProps';

export const Button: FC<ButtonProps> = ({ text }) => <button>{text}</button>;