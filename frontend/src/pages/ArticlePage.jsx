import { ExternalLink } from 'lucide-react';
import { useEffect, useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import ArticleCard, { ArticleImage, formatDate } from '../components/ArticleCard.jsx';
import LoadingSpinner from '../components/LoadingSpinner.jsx';
import SourceBadge from '../components/SourceBadge.jsx';
import { getArticle, getNews } from '../lib/api.js';
import { setSeo } from '../lib/seo.js';

export default function ArticlePage() {
  const { slug } = useParams();
  const [state, setState] = useState({ loading: true, article: null, related: [] });

  useEffect(() => {
    getArticle(slug).then((article) => {
      setSeo({
        title: article.meta_title || article.title,
        description: article.meta_description || article.summary,
        image: article.image_url,
        type: 'article',
        jsonLd: {
          '@context': 'https://schema.org',
          '@type': 'NewsArticle',
          headline: article.title,
          description: article.meta_description || article.summary,
          datePublished: article.original_published_at,
          dateModified: article.published_on_site_at,
          author: article.author ? { '@type': 'Person', name: article.author } : { '@type': 'Organization', name: article.source?.name || 'World Tech Khabar' },
          publisher: { '@type': 'Organization', name: 'World Tech Khabar' },
          mainEntityOfPage: window.location.href
        }
      });
      getNews({ category: article.category?.slug }).then((news) => {
        setState({ loading: false, article, related: (news.data || []).filter((item) => item.slug !== article.slug).slice(0, 3) });
      });
    }).catch(() => setState({ loading: false, article: null, related: [] }));
  }, [slug]);

  if (state.loading) return <LoadingSpinner />;
  if (!state.article) return <main className="mx-auto max-w-4xl px-4 py-16"><h1 className="text-3xl font-black">Article not found</h1></main>;

  const { article } = state;

  return (
    <main className="mx-auto max-w-5xl px-4 py-10">
      <article className="article-content-card site-card rounded p-5 md:p-8">
        <div className="mb-5 flex flex-wrap items-center gap-2">
          {article.category && <Link to={`/category/${article.category.slug}`} className="rounded bg-blue-50 px-3 py-1 text-sm font-black text-blue-800 ring-1 ring-blue-100">{article.category.name}</Link>}
          <SourceBadge source={article.source} />
        </div>
        <h1 className="max-w-4xl text-4xl font-black leading-tight md:text-5xl">{article.title}</h1>
        <div className="mt-5 flex flex-wrap gap-4 text-sm text-slate-500">
          <span>Original: {formatDate(article.original_published_at)}</span>
          <span>Published: {formatDate(article.published_on_site_at)}</span>
          {article.author && <span>By {article.author}</span>}
        </div>
        <div className="mt-8 overflow-hidden rounded"><ArticleImage article={article} large /></div>
        <section className="prose-news mt-8">
          {article.summary && <p className="lead">{article.summary}</p>}
          {article.ai_summary && <p>{article.ai_summary}</p>}
          {article.content_excerpt && <p>{article.content_excerpt}</p>}
          {!article.summary && !article.ai_summary && !article.content_excerpt && (
            <p className="lead">This source did not provide a public summary in its feed. Use the original source button below to read the full official update.</p>
          )}
        </section>
        <div className="mt-8 flex flex-wrap gap-2">
          {(article.tags || []).map((tag) => <Link key={tag} to={`/search?q=${encodeURIComponent(tag)}`} className="rounded bg-slate-50 px-3 py-1 text-sm font-bold text-slate-700 ring-1 ring-slate-200 hover:text-blue-700">{tag}</Link>)}
        </div>
        <a href={article.original_url} target="_blank" rel="noreferrer" className="mt-8 inline-flex items-center gap-2 rounded border border-yellow-300 bg-yellow-300 px-5 py-3 font-black text-slate-950 shadow-lg shadow-yellow-200/70 transition hover:border-cyan-300 hover:bg-cyan-300 hover:text-slate-950">
          Read original source <ExternalLink className="h-4 w-4" />
        </a>
      </article>
      {state.related.length > 0 && (
        <section className="mt-14">
          <h2 className="text-2xl font-black text-slate-950">Related Articles</h2>
          <div className="mt-5 grid gap-5 md:grid-cols-3">{state.related.map((item) => <ArticleCard key={item.id} article={item} />)}</div>
        </section>
      )}
    </main>
  );
}
