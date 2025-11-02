interface GreetingProps {
    name: string; // описываем проп "name" типа string
    id?: string;
}

export function Greeting({ name, id }: GreetingProps) {
    return <p>Привет, {name} id = {id}!</p>;
}