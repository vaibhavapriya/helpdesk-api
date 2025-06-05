@extends('components.layouts.app.client')
@section('content')
    <div class="container">
        <h1>Ticket Details</h1>
        <div class="mb-3">
            <strong>Title:</strong> {{ $ticket->title }}
        </div>
        <div class="mb-3">
            <strong>Description:</strong> {{ $ticket->description }}
        </div>
        <div class="mb-3">
            <strong>Priority:</strong> {{ ucfirst($ticket->priority) }}
        </div>
        <div class="mb-3">
            <strong>Status:</strong> {{ ucfirst($ticket->status) }}
        </div>
        <div class="mb-3">
            <strong>Department:</strong> {{ $ticket->department }}
        </div>
        <div class="mb-3">
            <strong>Requester:</strong> {{ $ticket->requester->name }}
        </div>
        <img src="{{ Storage::url($ticket->filelink) }}" alt="Ticket Attachment" style="max-width: 300px;">

    </div>
    <div class="container ">
        <div class="row">
            <div class="col-12">
                <!-- <div class="blog-comment col-12"> -->
                    <h3 class="text-success">Replies</h3>
                    <hr/>
                    <ul class="comments">
                        @foreach($ticket->replies as $reply)
                            <li class="clearfix">
                                <!-- <img src="{{ $reply->user->avatar_url ?? 'https://bootdey.com/img/Content/user_1.jpg' }}" class="avatar" alt="User Avatar"> -->
                                <div class="post-comments">
                                    <p class="meta">
                                        {{ $reply->created_at->format('M d, Y') }}
                                        <a href="#">{{ $reply->user->name ?? 'Anonymous' }}</a> says :
                                        <i class="pull-right"><a href="#"><small>Reply</small></a></i>
                                    </p>
                                    <p>{{ $reply->reply }}</p>
                                </div>

                                {{-- Optional: If you have nested replies, you can recursively show them here --}}
                                @if($reply->children && $reply->children->count() > 0)
                                    <ul class="comments">
                                        @foreach($reply->children as $childReply)
                                            <li class="clearfix">
                                                <img src="{{ $childReply->user->avatar_url ?? 'https://bootdey.com/img/Content/user_2.jpg' }}" class="avatar" alt="User Avatar">
                                                <div class="post-comments">
                                                    <p class="meta">
                                                        {{ $childReply->created_at->format('M d, Y') }}
                                                        <a href="#">{{ $childReply->user->name ?? 'Anonymous' }}</a> says :
                                                        <i class="pull-right"><a href="#"><small>Reply</small></a></i>
                                                    </p>
                                                    <p>{{ $childReply->body }}</p>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    
                        <li class="clearfix col-12">
                            <img src="{{ $reply->user->avatar_url ?? 'https://bootdey.com/img/Content/user_1.jpg' }}" class="avatar" alt="User Avatar">
                            <div class="post-comments">
                                <form action="{{ route('comment',$ticket->id) }}" method="POST" class="d-flex align-items-center">
                                    @csrf

                                    <input class="form-control me-2" id="reply" name="reply" type="text" placeholder="Reply here">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </form>
                            </div>
                        </li>

                    </ul>
                <!-- </div> -->
            </div>
        </div>
        <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Back to List</a>
    </div>

@endsection