(() => {
  const search = document.querySelector('.search-box input[name="q"]');
  const courseCards = [...document.querySelectorAll('.course-card')];
  search?.addEventListener('input', () => {
    const query = search.value.trim().toLocaleLowerCase('uz');
    courseCards.forEach(card => { card.hidden = !card.textContent.toLocaleLowerCase('uz').includes(query); });
  });

  document.addEventListener('keydown', event => {
    if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
      event.preventDefault(); search?.focus(); search?.select();
    }
    if (event.key === 'Escape') document.querySelector('.sidebar')?.classList.remove('is-open');
  });

  const menuButton = document.querySelector('.menu-toggle');
  menuButton?.addEventListener('click', () => document.querySelector('.sidebar')?.classList.toggle('is-open'));
  document.addEventListener('click', event => {
    const dismiss = event.target.closest('[data-dismiss]');
    if (dismiss) dismiss.closest('.flash')?.remove();
  });
  document.querySelectorAll('form[data-confirm]').forEach(form => form.addEventListener('submit', event => {
    if (!window.confirm(form.dataset.confirm || 'Davom etasizmi?')) event.preventDefault();
  }));

  const dataNode = document.querySelector('#word-data');
  const cards = (() => {
    if (!dataNode) return [];
    try {
      const value = JSON.parse(dataNode.textContent || '[]');
      return Array.isArray(value) ? value : [];
    } catch (error) { console.error('So‘zlar ro‘yxatini o‘qib bo‘lmadi.', error); return []; }
  })();
  if (!cards.length) return;

  const card = document.querySelector('[data-flashcard]');
  const revealButton = document.querySelector('[data-reveal]');
  const translation = document.querySelector('[data-translation]');
  const answers = document.querySelector('[data-answers]');
  const previousButton = document.querySelector('[data-prev]');
  const nextButton = document.querySelector('[data-next-card]');
  const countLabel = document.querySelector('[data-card-count]');
  const progress = document.querySelector('[data-study-progress]');
  let index = 0;
  const startIndex = Number(dataNode.dataset.start || 0) % cards.length;
  const setTranslation = value => {
    if (!translation) return;
    const example = translation.querySelector('[data-example]');
    const text = document.createTextNode(`${value || ''}`);
    [...translation.childNodes].forEach(node => { if (node !== example) node.remove(); });
    translation.insertBefore(text, example || null);
  };
  const setWord = (selector, value) => {
    const target = document.querySelector(selector);
    if (target) target.textContent = value || '';
  };
  const render = () => {
    const current = cards[index];
    setWord('[data-term]', current.term);
    const pron = document.querySelector('[data-pronunciation]');
    if (pron) {
      pron.textContent = current.pronunciation ? `/ ${current.pronunciation} /` : '';
      pron.hidden = !current.pronunciation;
    }
    setWord('[data-example]', current.example_sentence);
    setTranslation(current.translation);
    setWord('.flashcard > .pill', current.lesson_title || current.course_title || 'So‘z');
    const position = (startIndex + index) % cards.length;
    document.querySelectorAll('[data-answers] input[name="vocabulary_id"]').forEach(input => { input.value = current.id; });
    document.querySelectorAll('[data-answers] input[name="cursor"]').forEach(input => { input.value = String(position); });
    if (countLabel) countLabel.textContent = `${position + 1} / ${cards.length}`;
    if (progress) progress.style.width = `${((position + 1) / cards.length) * 100}%`;
    if (previousButton) previousButton.disabled = cards.length < 2;
    if (nextButton) nextButton.disabled = cards.length < 2;
    card?.classList.remove('is-revealed');
    translation?.classList.add('hidden');
    answers?.classList.add('hidden');
    if (revealButton) {
      revealButton.hidden = false;
      revealButton.setAttribute('aria-expanded', 'false');
    }
  };
  const reveal = () => {
    const show = translation?.classList.contains('hidden') ?? true;
    translation?.classList.toggle('hidden', !show);
    answers?.classList.toggle('hidden', !show);
    card?.classList.toggle('is-revealed', show);
    if (revealButton) {
      revealButton.hidden = show;
      revealButton.setAttribute('aria-expanded', String(show));
    }
  };
  revealButton?.addEventListener('click', reveal);
  card?.addEventListener('keydown', event => {
    if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); reveal(); }
  });
  previousButton?.addEventListener('click', () => { index = (index - 1 + cards.length) % cards.length; render(); });
  nextButton?.addEventListener('click', () => { index = (index + 1) % cards.length; render(); });
  render();
})();
