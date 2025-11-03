import {GreetingProps} from '../interfaces/GreetingProps'

export function Greeting({ name, id }: GreetingProps) {
    return <p>Привет, {name} id = {id}!</p>;
}