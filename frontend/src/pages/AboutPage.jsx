import { useEffect } from 'react';
import { setSeo } from '../lib/seo.js';

export default function AboutPage() {
  useEffect(() => setSeo({ title: 'About', description: 'About World Tech Khabar and its official-source news workflow.' }), []);
  return (
    <main className="mx-auto max-w-4xl px-4 py-10">
      <section className="site-card rounded p-6 md:p-8">
        <h1 className="text-4xl font-black">About World Tech Khabar</h1>
        <div className="prose-news mt-6">
        <p>World Tech Khabar is an AI and technology news website focused on verified updates from official and free sources.</p>
        <p>The system collects article metadata from RSS feeds, official APIs such as arXiv and GitHub releases, and allowed public source pages. It stores titles, short excerpts, short summaries, dates, source metadata, and original links.</p>
        <p>We do not republish full copyrighted articles. Every story includes a clear button to read the original source.</p>
        </div>
      </section>
    </main>
  );
}
