<!-- resources/views/elements/feed/post-box-debug.blade.php -->
<!-- DEBUGGING VERSION - Replace your post-box.blade.php with this temporarily -->

<div class="post-box" data-post-id="{{$post->id}}">
    <div class="card mb-3 border-danger">
        <div class="card-header bg-danger text-white">
            <h5>DEBUG POST #{{$post->id}}</h5>
        </div>
        <div class="card-body">
            <!-- Debug Information Grid -->
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info">
                        <h6>POST OWNER INFO:</h6>
                        <ul class="mb-0">
                            <li>Post Owner ID: <strong>{{$post->user_id}}</strong></li>
                            <li>Post Owner Username: <strong>{{$post->user->username ?? 'N/A'}}</strong></li>
                            <li>Is Paid Profile: <strong>{{$post->user->paid_profile ? 'YES (PAID)' : 'NO (FREE)'}}</strong></li>
                            <li>Is Open Profile: <strong>{{$post->user->open_profile ? 'YES (OPEN)' : 'NO (CLOSED)'}}</strong></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="alert alert-warning">
                        <h6>VIEWER INFO:</h6>
                        <ul class="mb-0">
                            <li>Your ID: <strong>{{Auth::check() ? Auth::user()->id : 'NOT LOGGED IN'}}</strong></li>
                            <li>Are you owner?: <strong>{{Auth::check() && Auth::user()->id == $post->user_id ? 'YES' : 'NO'}}</strong></li>
                            <li>Are you admin?: <strong>{{Auth::check() && Auth::user()->role_id == 1 ? 'YES' : 'NO'}}</strong></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="alert alert-success">
                        <h6>POST SETTINGS:</h6>
                        <ul class="mb-0">
                            <li>Post Price: <strong>${{$post->price ?? 0}}</strong></li>
                            <li>Is PPV: <strong>{{$post->price > 0 ? 'YES' : 'NO (FREE)'}}</strong></li>
                            <li>Has Attachments: <strong>{{count($post->attachments) > 0 ? 'YES ('.count($post->attachments).')' : 'NO'}}</strong></li>
                            <li>Content Type: <strong>{{$post->content_type ?? 'N/A'}}</strong></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- The Critical Debug Info -->
            <div class="alert {{ $post->isSubbed ? 'alert-success' : 'alert-danger' }} mt-3">
                <h5>🔍 SUBSCRIPTION STATUS:</h5>
                <p class="mb-2"><strong>isSubbed Value: {{ $post->isSubbed ? 'TRUE ✅' : 'FALSE ❌' }}</strong></p>
                <p class="mb-2"><strong>hasSub Value: {{ isset($post->hasSub) ? ($post->hasSub ? 'TRUE ✅' : 'FALSE ❌') : 'NOT SET' }}</strong></p>
                
                @php
                    // Manual subscription check
                    $hasActiveSubManual = false;
                    if(Auth::check() && $post->user_id != Auth::user()->id) {
                        $hasActiveSubManual = \App\Providers\PostsHelperServiceProvider::hasActiveSub(Auth::user()->id, $post->user_id);
                    }
                @endphp
                
                <p class="mb-0"><strong>Manual Sub Check: {{ $hasActiveSubManual ? 'YES - YOU HAVE ACTIVE SUB ✅' : 'NO - NO ACTIVE SUB ❌' }}</strong></p>
            </div>
            
            <!-- Why is it locked/unlocked? -->
            <div class="alert alert-primary">
                <h6>WHY IS THIS POST {{ $post->isSubbed ? 'UNLOCKED' : 'LOCKED' }}?</h6>
                @if(Auth::check())
                    @if(Auth::user()->id == $post->user_id)
                        <p>✅ You own this post</p>
                    @elseif(Auth::user()->role_id == 1)
                        <p>✅ You are an admin</p>
                    @elseif($post->user->open_profile && getSetting('profiles.allow_users_enabling_open_profiles'))
                        <p>✅ This is an open profile</p>
                    @elseif(!$post->user->paid_profile)
                        <p>✅ This is a FREE profile</p>
                    @elseif($post->isSubbed)
                        <p>✅ You have subscription access</p>
                    @else
                        <p>❌ You need a subscription (paid profile, not subscribed)</p>
                    @endif
                @else
                    <p>❌ You are not logged in</p>
                @endif
            </div>
            
            <!-- Show actual subscription data from DB -->
            @if(Auth::check())
                @php
                    $subscription = \App\Model\Subscription::where('sender_user_id', Auth::user()->id)
                        ->where('recipient_user_id', $post->user_id)
                        ->first();
                @endphp
                <div class="alert alert-dark">
                    <h6>DATABASE SUBSCRIPTION CHECK:</h6>
                    @if($subscription)
                        <ul class="mb-0">
                            <li>Subscription ID: <strong>{{$subscription->id}}</strong></li>
                            <li>Status: <strong>{{$subscription->status}}</strong></li>
                            <li>Expires: <strong>{{$subscription->expires_at}}</strong></li>
                            <li>Is Active: <strong>{{$subscription->status == 'completed' || ($subscription->status == 'canceled' && $subscription->expires_at > \Carbon\Carbon::now()) ? 'YES ✅' : 'NO ❌'}}</strong></li>
                        </ul>
                    @else
                        <p class="mb-0">NO SUBSCRIPTION FOUND IN DATABASE ❌</p>
                    @endif
                </div>
            @endif
            
            <!-- Original Post Media (Small Preview) -->
            <div class="mt-3 p-2 border">
                <small class="text-muted">Original Content Preview:</small>
                @if(count($post->attachments) > 0)
                    <div style="max-height: 100px; overflow: hidden;">
                        @if($post->isSubbed || (Auth::check() && Auth::user()->id == $post->user_id))
                            <img src="{{$post->attachments[0]->path}}" class="img-fluid" style="max-height: 100px;">
                        @else
                            <div class="bg-secondary text-white p-3">🔒 LOCKED CONTENT</div>
                        @endif
                    </div>
                @else
                    <p class="small">No attachments</p>
                @endif
                <p class="small mt-2">Text: {{ Str::limit($post->text, 50) }}</p>
            </div>
        </div>
    </div>
</div>