export function UserInfo({ isLoggedIn }: { isLoggedIn: boolean }) {
    return (
        <div>
            {isLoggedIn ? (
                <p>Welcome back!</p>
            ) : (
                <p>Please log in.</p>
            )}
        </div>
    );
}
    