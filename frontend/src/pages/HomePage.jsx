import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import ArticleCard, { articleExcerpt, formatDate } from '../components/ArticleCard.jsx';
import FeaturedArticle from '../components/FeaturedArticle.jsx';
import LoadingSpinner from '../components/LoadingSpinner.jsx';
import SidebarTrending from '../components/SidebarTrending.jsx';
import { getCategories, getLatest, getNews, getSources, getTrending } from '../lib/api.js';
import { setSeo } from '../lib/seo.js';

const sectionSlugs = ['ai-news', 'ai-research', 'ai-tools', 'ai-regulation', 'big-tech', 'open-source'];
const threeNewsSectionSlugs = new Set(['ai-regulation', 'ai-research', 'open-source']);

export default function HomePage() {
  const [state, setState] = useState({ loading: true, latest: [], trending: [], categories: [], sources: [], sections: {} });

  useEffect(() => {
    setSeo({ title: 'World Tech Khabar', description: 'Verified AI and technology updates from official sources.' });
    Promise.all([
      getLatest(),
      getTrending(),
      getCategories(),
      getSources(),
      Promise.all(sectionSlugs.map((slug) => getNews({ category: slug }).then((result) => [slug, result.data || []])))
    ]).then(([latest, trending, categories, sources, sectionPairs]) => {
      setState({ loading: false, latest, trending, categories, sources, sections: Object.fromEntries(sectionPairs) });
    }).catch(() => setState((current) => ({ ...current, loading: false })));
  }, []);

  if (state.loading) return <LoadingSpinner />;

  const featured = state.latest[0];
  const topStories = state.latest.slice(1, 4);
  const latestList = state.latest.length > 6 ? state.latest.slice(4, 10) : state.latest.slice(1, 10);

  return (
    <main>
      <section className="mx-auto max-w-7xl px-4 py-10">
        <div className="grid gap-8 lg:grid-cols-[1.55fr_1fr]">
          <div>
            <FeaturedArticle article={featured} />
          </div>
          <div className="site-card-flat rounded p-5">
            <div className="section-title mb-5">
              <h2 className="text-xl font-black text-slate-950">Top Stories</h2>
            </div>
            <div className="grid gap-5">
              {topStories.map((article) => (
                <article key={article.id} className="border-b border-slate-200 pb-5 last:border-0 last:pb-0">
                  <div className="mb-2 flex items-center gap-2 text-xs font-black uppercase tracking-wide text-blue-700">
                    <span>{article.category?.name}</span>
                    <span className="text-slate-300">/</span>
                    <time className="text-slate-500">{formatDate(article.published_on_site_at || article.original_published_at)}</time>
                  </div>
                  <Link to={`/news/${article.slug}`} className="text-xl font-black leading-tight text-slate-950 hover:text-blue-700">{article.title}</Link>
                  <p className="mt-3 line-clamp-3 text-sm leading-6 text-slate-600">{articleExcerpt(article)}</p>
                </article>
              ))}
            </div>
          </div>
        </div>
      </section>

      <div className="mx-auto grid max-w-7xl gap-10 px-4 py-6 lg:grid-cols-[1fr_330px]">
        <div>
          <section className="mb-14">
            <div className="mb-6 flex items-center justify-between gap-4">
              <div className="section-title">
                <h2 className="text-3xl font-black text-slate-950">Latest News</h2>
              </div>
              <Link to="/search" className="text-sm font-black text-blue-700 hover:text-blue-900">Explore all</Link>
            </div>
            <div className="site-card-flat rounded p-6">
              {latestList.length > 0 ? (
                <div className="grid gap-6">
                  {latestList.map((article) => <ArticleCard key={article.id} article={article} variant="list" />)}
                </div>
              ) : (
                <p className="text-slate-600">No published articles yet. Fetch and publish news from the admin dashboard to populate this section.</p>
              )}
            </div>
          </section>
          {sectionSlugs.map((slug) => {
            const category = state.categories.find((item) => item.slug === slug);
            const limit = threeNewsSectionSlugs.has(slug) ? 3 : 4;
            return <Section key={slug} title={category?.name || slug} articles={(state.sections[slug] || []).slice(0, limit)} link={`/category/${slug}`} />;
          })}
        </div>
        <SidebarTrending trending={state.trending} sources={state.sources} />
      </div>
    </main>
  );
}

function Section({ title, articles = [], link }) {
  if (!articles.length) return null;
  return (
    <section className="mb-14">
      <div className="mb-5 flex items-center justify-between gap-4">
        <div className="section-title">
          <h2 className="text-2xl font-black text-slate-950">{title}</h2>
        </div>
        {link && <Link to={link} className="text-sm font-black text-blue-700 hover:text-blue-900">View all</Link>}
      </div>
      <div className={`grid gap-5 ${articles.length === 3 ? 'md:grid-cols-3' : 'md:grid-cols-2 xl:grid-cols-4'}`}>
        {articles.map((article) => <ArticleCard key={article.id} article={article} />)}
      </div>
    </section>
  );
}
