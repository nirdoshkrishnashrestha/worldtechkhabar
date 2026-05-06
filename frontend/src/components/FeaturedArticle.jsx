import { Link } from 'react-router-dom';
import { ArticleImage, articleExcerpt, formatDate } from './ArticleCard.jsx';
import SourceBadge from './SourceBadge.jsx';

export default function FeaturedArticle({ article }) {
  if (!article) return null;

  return (
    <article className="site-card group overflow-hidden rounded">
      <Link to={`/news/${article.slug}`} className="block overflow-hidden">
        <ArticleImage article={article} large />
      </Link>
      <div className="p-6 md:p-8">
        <div className="mb-4 flex flex-wrap gap-2">
          <span className="rounded bg-emerald-50 px-3 py-1 text-xs font-black uppercase tracking-wide text-emerald-800 ring-1 ring-emerald-100">Official Source</span>
          {article.category && <span className="rounded bg-blue-50 px-3 py-1 text-xs font-black uppercase tracking-wide text-blue-800 ring-1 ring-blue-100">{article.category.name}</span>}
        </div>
        <Link to={`/news/${article.slug}`} className="text-3xl font-black leading-tight hover:text-blue-700 md:text-5xl">{article.title}</Link>
        <p className="mt-5 line-clamp-4 text-[17px] leading-8 text-slate-600">{articleExcerpt(article)}</p>
        <div className="mt-6 flex flex-wrap items-center gap-4 text-sm text-slate-500">
          <SourceBadge source={article.source} />
          <time>{formatDate(article.published_on_site_at)}</time>
          <Link to={`/news/${article.slug}`} className="font-black text-blue-700 hover:text-blue-900">Continue reading</Link>
        </div>
      </div>
    </article>
  );
}
