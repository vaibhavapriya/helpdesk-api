<section class="d-flex align-items-center justify-content-center">
  <div class="container">
    <div class="row g-4 mb-5" id="home-cards-container">
      <!-- Cards will be inserted here -->
    </div>
  </div>
</section>

<script>
  // JS component to generate the card HTML
  function HomeCard({ url, icon, title, colClass }) {
    return `
      <div class="${colClass}">
        <a href="${url}" class="text-decoration-none text-dark">
          <div class="card text-center shadow-sm p-4 h-100">
            <i class="${icon} fa-2x mb-3"></i>
            <h5>${title}</h5>
          </div>
        </a>
      </div>
    `;
  }

  // Check if user is authenticated by checking token in localStorage
  const token = localStorage.getItem('auth_token'); // Adjust key if needed
  const isAuthenticated = !!token;

  // Cards to render
  const cards = [];

  if (!isAuthenticated) {
    cards.push({
      url: "{{ route('register') }}",
      icon: 'fa-regular fa-pen-to-square',
      title: 'Register',
      colClass: 'col-md-3'
    });
  }

  const colClass = isAuthenticated ? 'col-md-4' : 'col-md-3';

  cards.push(
    {
      url: "{{ route('ticket') }}",
      icon: 'fa-solid fa-rectangle-list',
      title: 'Submit Ticket',
      colClass
    },
    {
      url:"{{ route('tickets') }}",
      icon: 'fa-regular fa-newspaper',
      title: 'My Tickets',
      colClass
    },
    {
      url: "{{ route('kb') }}",
      icon: 'fa-solid fa-lightbulb',
      title: 'Knowledge Base',
      colClass
    }
  );

  // Insert cards into container
  const container = document.getElementById('home-cards-container');
  container.innerHTML = cards.map(card => HomeCard(card)).join('');
</script>



