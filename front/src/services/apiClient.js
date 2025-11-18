const API_BASE_URL = "http://127.0.0.1:8000/api";

export async function apiRequest(path, options = {}) {
  const { method = "GET", body, headers } = options;

  const res = await fetch(`${API_BASE_URL}${path}`, {
    method,
    headers: {
      "Content-Type": "application/json",
      ...headers,
    },
    body: body ? JSON.stringify(body) : undefined,
  });

  if (!res.ok) {
    const message = await res.text().catch(() => null);
    throw new Error(message || `Erreur API ${res.status}`);
  }

  if (res.status === 204) return null;
  return res.json();
}