import axios from 'axios';

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://127.0.0.1:8000/api';

export const api = axios.create({
  baseURL: API_BASE_URL,
  timeout: 15000
});

export const unwrap = (response) => response.data?.data ?? response.data;

export async function getNews(params = {}) {
  return api.get('/news', { params }).then((response) => response.data);
}

export async function getArticle(slug) {
  return api.get(`/news/${slug}`).then(unwrap);
}

export async function getCategories() {
  return api.get('/categories').then(unwrap);
}

export async function getSources() {
  return api.get('/sources').then(unwrap);
}

export async function getLatest() {
  return api.get('/latest').then(unwrap);
}

export async function getTrending() {
  return api.get('/trending').then(unwrap);
}

export async function searchNews(q, page = 1) {
  return api.get('/search', { params: { q, page } }).then((response) => response.data);
}
