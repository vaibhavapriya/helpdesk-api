<section class="d-flex align-items-center justify-content-center">
    <div class="container">
        <div class="row g-4 mb-5">
            @guest
            <x-home-card 
                url="{{ route('register') }}" 
                icon="fa-regular fa-pen-to-square" 
                title="Register" 
                :col="auth()->check() ? 'col-md-4' : 'col-md-3'" />
            @endguest

            <x-home-card 
                url="{{ route('ticket') }}" 
                icon="fa-solid fa-rectangle-list" 
                title="Submit Ticket" 
                :col="auth()->check() ? 'col-md-4' : 'col-md-3'" />

            <x-home-card 
                url="{{ route('tickets') }}" 
                icon="fa-regular fa-newspaper" 
                title="My Ticket" 
                :col="auth()->check() ? 'col-md-4' : 'col-md-3'" />

            <x-home-card 
                url="{{ route('kb') }}" 
                icon="fa-solid fa-lightbulb" 
                title="Knowledge Base" 
                :col="auth()->check() ? 'col-md-4' : 'col-md-3'" />
        </div>
    </div>
</section>
