interface GreetingProps {
    name: string; // описываем проп "name" типа string
}

export function Greeting({ name }: GreetingProps) {
    return <p>Привет, {name}!</p>;
}