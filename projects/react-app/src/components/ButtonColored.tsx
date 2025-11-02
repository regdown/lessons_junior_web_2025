import { FC } from 'react';
import { ButtonProps } from '../interfaces/ButtonProps';

export const ButtonColored: FC<ButtonProps> = ({ text, color }) => <button style={{ color: color }}>{text}</button>;