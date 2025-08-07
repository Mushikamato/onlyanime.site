@extends('voyager::master')

@section('page_title', 'Artificial Post Manager')

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-rocket"></i>
        Artificial Post Manager
    </h1>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        
                        @if (session('success_message'))
                            <div class="alert alert-success">
                                {{ session('success_message') }}
                            </div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0" style="list-style:none; padding-left:0;">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.seeder.seedPosts') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="attachments">1. Add Attachments</label>
                                <input type="file" class="form-control" id="attachments" name="attachments[]" multiple required>
                                <small class="form-text text-muted">Select one or more image files. A post will be created for each file. You can adjust which user will make the post.</small>
                            </div>

                            <div class="form-group">
                                <label for="user_id">2. User Creating Posts</label>
                                <select id="user_id" name="user_id" class="form-control" required>
                                    <option value="" selected disabled>Choose a user...</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} (@{{$user->username}})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="post_type">3. Post Category</label>
                                <select id="post_type" name="post_type" class="form-control" required>
                                    <option value="" selected disabled>Choose a category...</option>
                                    <option value="cosplay_sfw">Cosplay</option>
                                    <option value="cosplay_nsfw">Cosplay 18+</option>
                                    <option value="anime_sfw">Anime</option>
                                    <option value="anime_nsfw">Anime 18+</option>
                                </select>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="likes">4. Average Number of Likes</label>
                                        <input type="number" class="form-control" id="likes" name="likes" value="25" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="like_spread">5. Like Spread (+/-)</label>
                                        <input type="number" class="form-control" id="like_spread" name="like_spread" value="15" required>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Seed Posts</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
