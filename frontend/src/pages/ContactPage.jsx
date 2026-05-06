import { useEffect } from 'react';
import { setSeo } from '../lib/seo.js';

export default function ContactPage() {
  useEffect(() => setSeo({ title: 'Contact', description: 'Contact World Tech Khabar.' }), []);
  return (
    <main className="mx-auto max-w-3xl px-4 py-10">
      <form className="site-card grid gap-4 rounded p-6 md:p-8" onSubmit={(event) => event.preventDefault()}>
        <h1 className="text-4xl font-black">Contact</h1>
        <div><label className="font-bold">Name</label><input className="mt-2 w-full rounded border border-slate-200 bg-white px-3 py-2 outline-none focus:border-blue-400" /></div>
        <div><label className="font-bold">Email</label><input className="mt-2 w-full rounded border border-slate-200 bg-white px-3 py-2 outline-none focus:border-blue-400" type="email" /></div>
        <div><label className="font-bold">Message</label><textarea className="mt-2 min-h-36 w-full rounded border border-slate-200 bg-white px-3 py-2 outline-none focus:border-blue-400" /></div>
        <button className="rounded bg-slate-950 px-5 py-3 font-black text-white hover:bg-blue-700">Send Message</button>
        <p className="text-sm text-slate-500">You can also email contact@worldtechkhabar.com.</p>
      </form>
    </main>
  );
}
