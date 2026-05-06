import { Search } from 'lucide-react';
import { useState } from 'react';
import { useNavigate } from 'react-router-dom';

export default function SearchBar({ defaultValue = '' }) {
  const [query, setQuery] = useState(defaultValue);
  const navigate = useNavigate();

  function submit(event) {
    event.preventDefault();
    if (query.trim()) navigate(`/search?q=${encodeURIComponent(query.trim())}`);
  }

  return (
    <form onSubmit={submit} className="flex overflow-hidden rounded border border-slate-200 bg-white shadow-sm">
      <div className="grid place-items-center px-3 text-slate-400"><Search className="h-5 w-5" /></div>
      <input value={query} onChange={(event) => setQuery(event.target.value)} className="min-w-0 flex-1 px-2 py-3 outline-none" placeholder="Search AI and technology news" />
      <button className="bg-slate-950 px-5 font-black text-white hover:bg-blue-700">Search</button>
    </form>
  );
}
