export default function NewsletterBox() {
  return (
    <section className="site-card-flat rounded p-5">
      <div className="section-title"><h2 className="text-lg font-black">Newsletter</h2></div>
      <p className="mt-3 text-sm leading-6 text-slate-600">Get a compact daily brief of official AI and technology updates.</p>
      <form className="mt-4 grid gap-3" onSubmit={(event) => event.preventDefault()}>
        <input className="rounded border border-slate-200 bg-white px-4 py-2 outline-none focus:border-blue-400" placeholder="Email address" type="email" />
        <button className="rounded bg-slate-950 px-4 py-2 font-black text-white hover:bg-blue-700">Subscribe</button>
      </form>
    </section>
  );
}
