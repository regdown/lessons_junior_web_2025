import React from 'react';
import styles from './Greeting.module.css';

type GreetingProps = {
    name: string;
};

export default function Greeting({ name }: GreetingProps) {
    return <h2 className={styles.title}>Привет, {name}!</h2>;
}
