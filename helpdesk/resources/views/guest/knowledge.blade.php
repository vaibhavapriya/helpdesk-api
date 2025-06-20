@extends('components.layouts.app.client')
@section('content')
<main class="d-flex align-items-center justify-content-center">
    <div class="container">
      <div class="d-flex justify-content-end mb-3">
        <select id="langSelector" class="form-select w-auto">
          <option value="en" selected>English</option>
          <option value="es">Español</option>
        </select>
      </div>

      <h2 class="mb-4 mt-4 text-center" id="pageTitle">Service Desk Knowledge Base</h2>

      <div class="accordion" id="faqAccordion">
        <!-- FAQs load here -->
      </div>

      <div class="alert alert-info mt-4" role="alert" id="supportAlert">
        <strong>Need more support?</strong> If you did not find an answer, please raise a ticket describing the issue.
      </div>
    </div>
</main>

<script>
  async function loadFaq() {
    const faqContainer = document.getElementById('faqAccordion');
    const pageTitle = document.getElementById('pageTitle');
    const supportAlert = document.getElementById('supportAlert');

    faqContainer.innerHTML = ''; // clear existing faqs

    try {
      const response = await fetch('/api/faq');
      if(!response.ok) throw new Error('Failed to fetch FAQs');

      const data = await response.json();

      pageTitle.textContent = data.title;
      supportAlert.innerHTML = data.support_alert;

      data.faqs.forEach((faq, index) => {
        const collapseId = `faq${index}`;
        const headingId = `heading${index}`;
        const isFirst = index === 0;

        const faqItem = document.createElement('div');
        faqItem.className = 'accordion-item';

        faqItem.innerHTML = `
          <h2 class="accordion-header" id="${headingId}">
            <button class="accordion-button ${isFirst ? '' : 'collapsed'}" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="${isFirst}" aria-controls="${collapseId}">
              ${faq.question}
            </button>
          </h2>
          <div id="${collapseId}" class="accordion-collapse collapse ${isFirst ? 'show' : ''}" aria-labelledby="${headingId}" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              ${faq.answer.map(line => `<p>${line}</p>`).join('')}
            </div>
          </div>
        `;

        faqContainer.appendChild(faqItem);
      });
    } catch (error) {
      faqContainer.innerHTML = `<p class="text-danger">Error loading FAQs: ${error.message}</p>`;
    }
  }

  async function setLocale(locale) {
    try {
      const response = await fetch('/api/locale', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ locale })
      });
      if(!response.ok) throw new Error('Failed to set locale');
      await response.json();
    } catch (error) {
      console.error('Error setting locale:', error);
    }
  }

  document.getElementById('langSelector').addEventListener('change', async (e) => {
    const locale = e.target.value;
    await setLocale(locale);
    await loadFaq();
  });

  document.addEventListener('DOMContentLoaded', () => {
    loadFaq();
  });
</script>
@endsection
