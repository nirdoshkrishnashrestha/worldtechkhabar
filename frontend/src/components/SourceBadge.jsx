export default function SourceBadge({ source }) {
  return <span className="rounded bg-emerald-50 px-2 py-1 text-xs font-black text-emerald-800 ring-1 ring-emerald-100">{source?.name || 'Official Source'}</span>;
}
