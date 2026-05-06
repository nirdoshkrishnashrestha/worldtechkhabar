import { useEffect, useState } from 'react';
import { useParams, useSearchParams } from 'react-router-dom';
import ArticleCard from '../components/ArticleCard.jsx';
import LoadingSpinner from '../components/LoadingSpinner.jsx';
import Pagination from '../components/Pagination.jsx';
import { getCategories, getNews } from '../lib/api.js';
import { setSeo } from '../lib/seo.js';

export default function CategoryPage() {
  const { slug } = useParams();
  const [params, setParams] = useSearchParams();
  const [state, setState] = useState({ loading: true, articles: [], meta: null, category: null });

  useEffect(() => {
    const page = params.get('page') || 1;
    Promise.all([getNews({ category: slug, page }), getCategories()]).then(([news, categories]) => {
      const category = categories.find((item) => item.slug === slug);
      setSeo({ title: category?.name || 'Category', description: category?.description });
      setState({ loading: false, articles: news.data || [], meta: news.meta, category });
    });
  }, [slug, params]);

  if (state.loading) return <LoadingSpinner />;

  return (
    <main className="mx-auto max-w-7xl px-4 py-10">
      <div className="site-card rounded p-6 md:p-8">
        <h1 className="text-4xl font-black">{state.category?.name || 'Category'}</h1>
        <p className="mt-3 max-w-3xl text-slate-600">{state.category?.description}</p>
      </div>
      <div className="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        {state.articles.map((article) => <ArticleCard key={article.id} article={article} />)}
      </div>
      <Pagination meta={state.meta} onPage={(page) => setParams({ page })} />
    </main>
  );
}
