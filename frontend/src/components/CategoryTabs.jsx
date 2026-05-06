import { Link } from 'react-router-dom';

export default function CategoryTabs({ categories = [] }) {
  return (
    <div className="flex gap-2 overflow-x-auto pb-2">
      {categories.map((category) => (
        <Link key={category.id} to={`/category/${category.slug}`} className="shrink-0 rounded border border-slate-200 bg-white px-4 py-2 text-sm font-black text-slate-700 hover:border-blue-300 hover:text-blue-700">
          {category.name}
        </Link>
      ))}
    </div>
  );
}
