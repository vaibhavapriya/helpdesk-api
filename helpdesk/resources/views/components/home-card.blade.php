<!-- resources/views/components/home-card.blade.php -->
<div class='col-12 col-sm-6 {{ $col }}'>
    <a href="{{ $url }}" class="text-decoration-none text-dark">
        <div class="card text-center shadow-sm bg-light">
            <div class="card-body">
                <i class="{{ $icon }} fa-2x mb-2"></i>
                <h5 class="card-title">{{ $title }}</h5>
            </div>
        </div>
    </a>
</div>
            <x-home-card 
                url="{{ route('kb') }}" 
                icon="fa-solid fa-lightbulb" 
                title="Knowledge Base" 
                :col="auth()->check() ? 'col-md-4' : 'col-md-3'" />