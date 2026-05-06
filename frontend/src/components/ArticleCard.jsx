import { Link } from 'react-router-dom';
import SourceBadge from './SourceBadge.jsx';

export default function ArticleCard({ article, variant = 'grid' }) {
  const excerpt = articleExcerpt(article);

  if (variant === 'list') {
    return (
      <article className="group grid gap-5 border-b border-slate-200 pb-6 md:grid-cols-[220px_1fr]">
        <Link to={`/news/${article.slug}`} className="block overflow-hidden rounded">
          <ArticleImage article={article} small />
        </Link>
        <div>
          <div className="mb-3 flex flex-wrap items-center gap-2">
            {article.category && <Link to={`/category/${article.category.slug}`} className="text-xs font-black uppercase tracking-wide text-blue-700">{article.category.name}</Link>}
            <span className="text-xs text-slate-400">/</span>
            <time className="text-xs font-semibold text-slate-500">{formatDate(article.published_on_site_at || article.original_published_at)}</time>
          </div>
          <Link to={`/news/${article.slug}`} className="text-2xl font-black leading-tight text-slate-950 transition group-hover:text-blue-700">{article.title}</Link>
          <p className="mt-3 line-clamp-4 text-[15px] leading-7 text-slate-600">{excerpt}</p>
          <div className="mt-4 flex flex-wrap items-center gap-3">
            <SourceBadge source={article.source} />
            <Link to={`/news/${article.slug}`} className="text-sm font-black text-blue-700 hover:text-blue-900">Read full summary</Link>
          </div>
        </div>
      </article>
    );
  }

  return (
    <article className="site-card-flat group overflow-hidden rounded transition duration-300 hover:-translate-y-0.5 hover:shadow-xl">
      <Link to={`/news/${article.slug}`} className="block overflow-hidden">
        <ArticleImage article={article} />
      </Link>
      <div className="p-5">
        <div className="mb-3 flex flex-wrap items-center gap-2">
          {article.category && <Link to={`/category/${article.category.slug}`} className="text-xs font-black uppercase tracking-wide text-blue-700">{article.category.name}</Link>}
          {article.score >= 80 && <span className="rounded bg-orange-50 px-2 py-1 text-xs font-black text-orange-800 ring-1 ring-orange-100">Priority</span>}
        </div>
        <Link to={`/news/${article.slug}`} className="line-clamp-2 text-lg font-black leading-tight text-slate-950 transition group-hover:text-blue-700">{article.title}</Link>
        <p className="mt-3 line-clamp-4 text-sm leading-6 text-slate-600">{excerpt}</p>
        <div className="mt-4 flex items-center justify-between gap-3 text-xs text-slate-500">
          <SourceBadge source={article.source} />
          <time>{formatDate(article.published_on_site_at || article.original_published_at)}</time>
        </div>
        <Link to={`/news/${article.slug}`} className="mt-4 inline-flex text-sm font-black text-blue-700 transition hover:text-blue-900">Read More</Link>
      </div>
    </article>
  );
}

export function ArticleImage({ article, large = false, small = false }) {
  if (article.image_url) {
    return <img src={article.image_url} alt="" className={`${large ? 'h-96' : small ? 'h-36 md:h-full' : 'h-48'} w-full object-cover transition duration-500 group-hover:scale-105`} loading="lazy" />;
  }

  return (
    <div className={`${large ? 'h-96' : small ? 'h-36 md:h-full' : 'h-48'} news-gradient grid place-items-center text-center text-white`}>
      <div className="px-4">
        <div className="text-sm font-black text-emerald-200">Official Source AI & Tech News</div>
        <div className="mt-2 text-xl font-black">World Tech Khabar</div>
      </div>
    </div>
  );
}

export function formatDate(value) {
  if (!value) return 'Recently';
  return new Intl.DateTimeFormat('en', { month: 'short', day: 'numeric', year: 'numeric' }).format(new Date(value));
}

export function articleExcerpt(article) {
  return article?.content_excerpt || article?.summary || article?.ai_summary || 'This official source did not provide a public excerpt. Open the article summary page to continue to the original source.';
}
