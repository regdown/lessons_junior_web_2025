import { FC } from 'react';
import { ButtonProps } from '../interfaces/ButtonProps';

export const ButtonHint: FC<ButtonProps> = ({ text, isHint }) => <button>{text} {isHint ? '(подсказка)' : ''}</button>;