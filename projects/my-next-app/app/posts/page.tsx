"use client";

import CounterControls from '@/components/CounterControls/CounterControls';
import { useEffect, useState } from 'react';

interface Post {
  id: number;
  title: string;
}

export default function PostsPage() {
  const [posts, setPosts] = useState<Post[] | null>(null);
  const [loading, setLoading] = useState<boolean>(false);
  useEffect(() => {
  async function loadPosts() {
    setLoading(true);
    try {
      const res = await fetch('https://jsonplaceholder.typicode.com/posts?_limit=5');
      const data: Post[] = await res.json();
      setPosts(data);
    } catch (error) {
      console.error('Ошибка при загрузке постов:', error);
    } finally {
      setLoading(false);
    }
  }
  loadPosts();
  }, []);
  return (
  <main>
    <h1>Список постов</h1>
    {loading && <p>Загрузка...</p>}
    {posts && (
      <ul>
        {posts.map(post => (
          <li key={post.id}>
            <strong>{post.title}</strong>
          </li>
        ))}
      </ul>
    )}
    <CounterControls />
  </main>
  );
}