import { Link } from 'react-router-dom';

export default function Footer() {
  return (
    <footer className="mt-16 border-t border-slate-200 bg-white text-slate-700">
      <div className="mx-auto grid max-w-7xl gap-8 px-4 py-10 md:grid-cols-4">
        <div>
          <div className="flex items-center">
            <img src="/logo.png" alt="World Tech Khabar logo" className="h-11 w-auto max-w-[220px] object-contain" onError={(event) => { event.currentTarget.style.display = 'none'; }} />
          </div>
          <p className="mt-4 text-sm text-slate-600">Verified AI and technology updates from official sources.</p>
        </div>
        <div><h3 className="font-bold">Categories</h3><div className="mt-3 grid gap-2 text-sm"><Link to="/category/ai-news">AI News</Link><Link to="/category/ai-research">AI Research</Link><Link to="/category/ai-tools">AI Tools</Link><Link to="/category/open-source">Open Source</Link></div></div>
        <div><h3 className="font-bold">Official Sources</h3><div className="mt-3 grid gap-2 text-sm"><Link to="/sources">Sources list</Link><Link to="/about">About</Link><Link to="/contact">Contact</Link></div></div>
        <div><h3 className="font-bold">Contact</h3><p className="mt-3 text-sm text-slate-600">contact@worldtechkhabar.com</p></div>
      </div>
      <div className="border-t border-slate-200 px-4 py-5 text-center text-xs text-slate-500">
        <p>World Tech Khabar summarizes and links to official sources. All original content belongs to respective owners.</p>
        <p className="mt-2">Copyright {new Date().getFullYear()} World Tech Khabar.</p>
      </div>
    </footer>
  );
}
