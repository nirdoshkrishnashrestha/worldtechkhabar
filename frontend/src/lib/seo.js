export function setSeo({ title, description, image, url, type = 'website', jsonLd }) {
  document.title = title ? `${title} | World Tech Khabar` : 'World Tech Khabar';
  setMeta('description', description || 'Verified AI and technology updates from official sources.');
  setMeta('og:title', title || 'World Tech Khabar', true);
  setMeta('og:description', description || 'Verified AI and technology updates from official sources.', true);
  setMeta('og:type', type, true);
  if (image) setMeta('og:image', image, true);
  if (url) setMeta('og:url', url, true);

  const existing = document.getElementById('json-ld');
  if (existing) existing.remove();
  if (jsonLd) {
    const script = document.createElement('script');
    script.id = 'json-ld';
    script.type = 'application/ld+json';
    script.textContent = JSON.stringify(jsonLd);
    document.head.appendChild(script);
  }
}

function setMeta(name, content, property = false) {
  const attr = property ? 'property' : 'name';
  let tag = document.head.querySelector(`meta[${attr}="${name}"]`);
  if (!tag) {
    tag = document.createElement('meta');
    tag.setAttribute(attr, name);
    document.head.appendChild(tag);
  }
  tag.setAttribute('content', content);
}
