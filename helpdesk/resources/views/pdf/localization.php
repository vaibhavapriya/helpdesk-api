@extends('components.layouts.app.client')
@section('content')
<main class="d-flex align-items-center justify-content-center">
  <div class="container">
    <h2 class="mb-4 mt-4 text-center">@lang('faq.title')</h2>

    <div class="accordion" id="faqAccordion">
      @foreach (Lang::get('faq.faqs') as $index => $faq)
        <div class="accordion-item">
          <h2 class="accordion-header" id="heading{{ $index }}">
            <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="faq{{ $index }}">
              {{ $faq['question'] }}
            </button>
          </h2>
          <div id="faq{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="heading{{ $index }}" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              @foreach ($faq['answer'] as $line)
                <p>{{ $line }}</p>
              @endforeach
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="alert alert-info mt-4" role="alert">
      {!! __('faq.support_alert') !!}
    </div>
  </div>
</main>
@endsection
