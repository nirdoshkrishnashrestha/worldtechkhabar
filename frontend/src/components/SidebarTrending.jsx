import { Link } from 'react-router-dom';
import NewsletterBox from './NewsletterBox.jsx';

export default function SidebarTrending({ trending = [], sources = [] }) {
  const tags = ['AI', 'LLM', 'GPU', 'Open Source', 'Research', 'Regulation', 'Cybersecurity', 'Cloud'];

  return (
    <aside className="space-y-6">
      <section className="site-card-flat rounded p-5">
        <div className="section-title"><h2 className="text-lg font-black">Trending</h2></div>
        <div className="mt-4 grid gap-4">
          {trending.slice(0, 6).map((article, index) => (
            <Link key={article.id} to={`/news/${article.slug}`} className="flex gap-3">
              <span className="grid h-8 w-8 shrink-0 place-items-center rounded bg-blue-700 text-sm font-black text-white">{index + 1}</span>
              <span className="text-sm font-bold leading-5 hover:text-blue-700">{article.title}</span>
            </Link>
          ))}
        </div>
      </section>
      <section className="site-card-flat rounded p-5">
        <div className="section-title"><h2 className="text-lg font-black">Official Sources</h2></div>
        <div className="mt-4 grid gap-2 text-sm">
          {sources.slice(0, 8).map((source) => <a key={source.id} href={source.official_url} target="_blank" rel="noreferrer" className="font-semibold text-slate-700 hover:text-blue-700">{source.name}</a>)}
        </div>
      </section>
      <NewsletterBox />
      <section className="site-card-flat rounded p-5">
        <div className="section-title"><h2 className="text-lg font-black">Popular Tags</h2></div>
        <div className="mt-4 flex flex-wrap gap-2">
          {tags.map((tag) => <Link key={tag} to={`/search?q=${encodeURIComponent(tag)}`} className="rounded bg-slate-50 px-3 py-1 text-sm font-bold text-slate-700 ring-1 ring-slate-200 hover:bg-blue-50 hover:text-blue-800">{tag}</Link>)}
        </div>
      </section>
    </aside>
  );
}
