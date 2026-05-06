import { Menu, Search, X } from 'lucide-react';
import { useState } from 'react';
import { Link, NavLink, useNavigate } from 'react-router-dom';

const nav = [
  ['Home', '/'],
  ['AI News', '/category/ai-news'],
  ['Research', '/category/ai-research'],
  ['Tools', '/category/ai-tools'],
  ['Regulation', '/category/ai-regulation'],
  ['Big Tech', '/category/big-tech'],
  ['Open Source', '/category/open-source'],
  ['Contact', '/contact']
];

export default function Header() {
  const [open, setOpen] = useState(false);
  const [query, setQuery] = useState('');
  const navigate = useNavigate();

  function submit(event) {
    event.preventDefault();
    if (query.trim()) navigate(`/search?q=${encodeURIComponent(query.trim())}`);
  }

  return (
    <header className="sticky top-0 z-40 border-b border-slate-200/80 bg-white/92 text-slate-950 shadow-sm backdrop-blur-xl">
      <div className="mx-auto flex max-w-7xl items-center gap-4 px-4 py-2.5">
        <Link to="/" className="flex min-w-0 items-center pr-2">
          <img src="/logo.png" alt="World Tech Khabar logo" className="h-10 w-auto max-w-[280px] object-contain md:h-14 md:max-w-[420px]" onError={(event) => { event.currentTarget.style.display = 'none'; }} />
          <span className="sr-only">World Tech Khabar</span>
        </Link>
        <nav className="ml-auto hidden items-center gap-1 lg:flex">
          {nav.map(([label, path]) => <NavLink key={path} to={path} className={({ isActive }) => `border-b-2 px-2.5 py-2 text-sm font-bold transition ${isActive ? 'border-blue-700 text-blue-700' : 'border-transparent text-slate-600 hover:border-slate-300 hover:text-slate-950'}`}>{label}</NavLink>)}
        </nav>
        <form onSubmit={submit} className="hidden items-center rounded border border-slate-200 bg-slate-50 px-3 md:flex">
          <Search className="h-4 w-4 text-slate-400" />
          <input value={query} onChange={(event) => setQuery(event.target.value)} className="w-40 bg-transparent px-2 py-2 text-sm text-slate-900 outline-none placeholder:text-slate-400" placeholder="Search news" />
        </form>
        <button className="ml-auto rounded p-2 text-slate-800 hover:bg-slate-100 lg:hidden" onClick={() => setOpen(!open)} aria-label="Toggle menu">
          {open ? <X /> : <Menu />}
        </button>
      </div>
      {open && (
        <div className="border-t border-slate-200 bg-white px-4 pb-4 lg:hidden">
          <form onSubmit={submit} className="mb-3 flex items-center rounded border border-slate-200 bg-slate-50 px-3">
            <Search className="h-4 w-4 text-slate-400" />
            <input value={query} onChange={(event) => setQuery(event.target.value)} className="w-full bg-transparent px-2 py-2 text-sm outline-none" placeholder="Search news" />
          </form>
          <div className="grid gap-1">
            {nav.map(([label, path]) => <NavLink key={path} to={path} onClick={() => setOpen(false)} className="rounded px-3 py-2 text-sm font-bold text-slate-700 hover:bg-slate-100">{label}</NavLink>)}
          </div>
        </div>
      )}
    </header>
  );
}
