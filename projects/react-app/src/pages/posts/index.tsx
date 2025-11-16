import { GetServerSideProps, NextPage } from 'next';
import React from 'react';

// Определяем тип данных поста
interface Post {
    id: number;
    title: string;
    body: string;
}

// Описываем пропсы компонента страницы
interface PostsPageProps {
    posts: Post[];
}

// Компонент страницы, отображает список постов
const PostsPage: NextPage<PostsPageProps> = ({ posts }) => {
return (
    <div>
        <h1>Список постов</h1>
        <ul>
            {posts.map(post => (
                <li key={post.id}>
                    <b>{post.title}</b>
                    <p>{post.body}</p>
                </li>
            ))}
        </ul>
    </div>
);
};
// Функция получения данных на сервере (SSR)
export const getServerSideProps: GetServerSideProps<PostsPageProps> = async() => {
    const res = await fetch('https://jsonplaceholder.typicode.com/posts?_limit=5');
    const posts: Post[] = await res.json();
    // Передаем данные в компонент через props
    return { props: { posts } };
};
export default PostsPage;