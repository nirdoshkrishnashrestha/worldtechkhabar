export default function Pagination({ meta, onPage }) {
  if (!meta || meta.last_page <= 1) return null;
  return (
    <div className="mt-8 flex items-center justify-center gap-3">
      <button disabled={meta.current_page <= 1} onClick={() => onPage(meta.current_page - 1)} className="rounded bg-slate-200 px-4 py-2 font-bold disabled:opacity-50">Previous</button>
      <span className="text-sm font-bold">Page {meta.current_page} of {meta.last_page}</span>
      <button disabled={meta.current_page >= meta.last_page} onClick={() => onPage(meta.current_page + 1)} className="rounded bg-slate-200 px-4 py-2 font-bold disabled:opacity-50">Next</button>
    </div>
  );
}
