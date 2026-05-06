import { useEffect, useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import ArticleCard from '../components/ArticleCard.jsx';
import LoadingSpinner from '../components/LoadingSpinner.jsx';
import Pagination from '../components/Pagination.jsx';
import SearchBar from '../components/SearchBar.jsx';
import { searchNews } from '../lib/api.js';
import { setSeo } from '../lib/seo.js';

export default function SearchPage() {
  const [params, setParams] = useSearchParams();
  const q = params.get('q') || '';
  const page = params.get('page') || 1;
  const [state, setState] = useState({ loading: true, articles: [], meta: null });

  useEffect(() => {
    setSeo({ title: q ? `Search: ${q}` : 'Search', description: 'Search World Tech Khabar AI and technology news.' });
    searchNews(q, page).then((news) => setState({ loading: false, articles: news.data || [], meta: news.meta }));
  }, [q, page]);

  return (
    <main className="mx-auto max-w-7xl px-4 py-10">
      <div className="site-card rounded p-6 md:p-8">
        <h1 className="text-4xl font-black">Search</h1>
        <div className="mt-6 max-w-2xl"><SearchBar defaultValue={q} /></div>
      </div>
      {state.loading ? <LoadingSpinner /> : (
        <>
          <div className="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3">{state.articles.map((article) => <ArticleCard key={article.id} article={article} />)}</div>
          <Pagination meta={state.meta} onPage={(nextPage) => setParams({ q, page: nextPage })} />
        </>
      )}
    </main>
  );
}
