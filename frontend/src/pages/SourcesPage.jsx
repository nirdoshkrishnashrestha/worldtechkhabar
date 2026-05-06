import { useEffect, useState } from 'react';
import LoadingSpinner from '../components/LoadingSpinner.jsx';
import { getSources } from '../lib/api.js';
import { setSeo } from '../lib/seo.js';

export default function SourcesPage() {
  const [sources, setSources] = useState(null);

  useEffect(() => {
    setSeo({ title: 'Official Sources', description: 'Official and free AI and technology sources used by World Tech Khabar.' });
    getSources().then(setSources);
  }, []);

  if (!sources) return <LoadingSpinner />;

  return (
    <main className="mx-auto max-w-7xl px-4 py-10">
      <div className="site-card rounded p-6 md:p-8">
        <h1 className="text-4xl font-black">Official Sources</h1>
        <p className="mt-3 max-w-3xl text-slate-600">World Tech Khabar uses official RSS feeds, public APIs, and allowed public pages. We store metadata, short excerpts, summaries, and links back to originals.</p>
      </div>
      <div className="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        {sources.map((source) => (
          <a key={source.id} href={source.official_url} target="_blank" rel="noreferrer" className="site-card-flat rounded p-5 transition hover:-translate-y-0.5 hover:shadow-xl">
            <div className="flex items-center gap-2"><strong>{source.name}</strong>{source.is_high_priority && <span className="rounded bg-orange-50 px-2 py-1 text-xs font-black text-orange-800 ring-1 ring-orange-100">Priority</span>}</div>
            <p className="mt-2 text-sm text-slate-600">{source.category?.name} | {source.trust_level} | {source.source_type}</p>
          </a>
        ))}
      </div>
    </main>
  );
}
