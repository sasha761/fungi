export async function fetchSummary(text, length, lang) {
  const response = await fetch('/wp-json/custom/v1/summarize', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ text, length, lang}),
  });

  if (!response.ok) {
    throw new Error('Failed to fetch summary');
  }

  const data = await response.json();
  return data.summary;
}
