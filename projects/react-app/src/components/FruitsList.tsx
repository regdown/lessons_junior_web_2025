export function FruitsList() {
    const fruits = ["Apple", "Banana", "Cherry"];
        return (
        <ul>
            {fruits.map(fruit => (
                <li key={fruit}>{fruit}</li>
            ))}
        </ul>
    );
}
    